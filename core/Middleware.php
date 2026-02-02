<?php
declare(strict_types=1);

namespace NextPHP\Core;

/**
 * Middleware Manager
 * 
 * Handles global, route-specific, and grouped middleware execution
 */
class Middleware
{
    private static array $globalMiddleware = [];
    private static array $routeMiddleware = [];
    private static array $middlewareGroups = [];
    private static array $aliases = [];
    
    /**
     * Register global middleware (runs on all routes)
     */
    public static function global(array $middleware): void
    {
        self::$globalMiddleware = array_merge(self::$globalMiddleware, $middleware);
    }
    
    /**
     * Register route-specific middleware
     * 
     * @param string $route Route pattern (e.g., '/admin/*', '/api/*', '/user/[id]')
     * @param array $middleware Array of middleware class names or callables
     */
    public static function route(string $route, array $middleware): void
    {
        self::$routeMiddleware[$route] = array_merge(
            self::$routeMiddleware[$route] ?? [],
            $middleware
        );
    }
    
    /**
     * Register a middleware group
     * 
     * @param string $name Group name (e.g., 'web', 'api', 'admin')
     * @param array $middleware Array of middleware class names
     */
    public static function group(string $name, array $middleware): void
    {
        self::$middlewareGroups[$name] = $middleware;
    }
    
    /**
     * Register middleware alias
     * 
     * @param string $alias Short name (e.g., 'auth', 'cors')
     * @param string $class Full class name
     */
    public static function alias(string $alias, string $class): void
    {
        self::$aliases[$alias] = $class;
    }
    
    /**
     * Resolve middleware from alias or class name
     */
    public static function resolve(string $middleware): ?object
    {
        // Check if it's an alias
        if (isset(self::$aliases[$middleware])) {
            $class = self::$aliases[$middleware];
        } else {
            $class = $middleware;
        }
        
        if (!class_exists($class)) {
            return null;
        }
        
        return new $class();
    }
    
    /**
     * Execute middleware chain
     * 
     * @param array $middleware Array of middleware to execute
     * @param callable $next The final handler (usually the page renderer)
     * @return mixed Response from middleware chain
     */
    public static function execute(array $middleware, callable $next)
    {
        // Build the middleware chain using array_reduce
        $chain = array_reduce(
            array_reverse($middleware),
            function ($nextMiddleware, $currentMiddleware) {
                return function ($request) use ($nextMiddleware, $currentMiddleware) {
                    return self::runMiddleware($currentMiddleware, $request, $nextMiddleware);
                };
            },
            $next
        );
        
        // Execute the chain with empty request object
        $request = new MiddlewareRequest();
        return $chain($request);
    }
    
    /**
     * Run a single middleware
     */
    private static function runMiddleware($middleware, $request, callable $next)
    {
        // If it's a string (class name or alias), resolve it
        if (is_string($middleware)) {
            $instance = self::resolve($middleware);
            if ($instance === null) {
                throw new \RuntimeException("Middleware not found: {$middleware}");
            }
            $middleware = $instance;
        }
        
        // If it's a callable (closure)
        if (is_callable($middleware)) {
            return $middleware($request, $next);
        }
        
        // If it's an object with handle method
        if (is_object($middleware) && method_exists($middleware, 'handle')) {
            return $middleware->handle($request, $next);
        }
        
        throw new \RuntimeException("Invalid middleware format");
    }
    
    /**
     * Get middleware for a specific route
     * 
     * @param string $uri Current request URI
     * @param string $routeName Route identifier (folder path)
     * @return array Combined middleware (global + route-specific + groups)
     */
    public static function getForRoute(string $uri, string $routeName): array
    {
        $middleware = self::$globalMiddleware;
        
        // Add route-specific middleware
        foreach (self::$routeMiddleware as $pattern => $routeMiddleware) {
            if (self::matchesRoute($pattern, $uri)) {
                $middleware = array_merge($middleware, $routeMiddleware);
            }
        }
        
        // Check for middleware groups based on route name
        // e.g., routes under /api get 'api' group middleware
        if (str_starts_with($uri, '/api')) {
            $middleware = array_merge($middleware, self::$middlewareGroups['api'] ?? []);
        }
        if (str_starts_with($uri, '/admin')) {
            $middleware = array_merge($middleware, self::$middlewareGroups['admin'] ?? []);
        }
        
        return array_unique($middleware);
    }
    
    /**
     * Check if route matches pattern
     */
    private static function matchesRoute(string $pattern, string $uri): bool
    {
        // Convert pattern to regex
        $pattern = preg_quote($pattern, '#');
        $pattern = str_replace('\*', '.*', $pattern);  // Wildcards
        $pattern = str_replace('\[', '(?P<', $pattern); // Dynamic segments start
        $pattern = str_replace('\]', '>[^/]+)', $pattern); // Dynamic segments end
        $pattern = '#^' . $pattern . '$#';
        
        return (bool) preg_match($pattern, $uri);
    }
    
    /**
     * Reset all middleware (useful for testing)
     */
    public static function reset(): void
    {
        self::$globalMiddleware = [];
        self::$routeMiddleware = [];
        self::$middlewareGroups = [];
        self::$aliases = [];
    }
    
    /**
     * Get all registered middleware info
     */
    public static function getAll(): array
    {
        return [
            'global' => self::$globalMiddleware,
            'route' => self::$routeMiddleware,
            'groups' => self::$middlewareGroups,
            'aliases' => self::$aliases,
        ];
    }
}

/**
 * Middleware Request Object
 * Passed through middleware chain containing request data
 */
class MiddlewareRequest
{
    public array $headers = [];
    public array $params = [];
    public array $query = [];
    public array $post = [];
    public string $method = 'GET';
    public string $uri = '';
    public array $attributes = []; // Custom data attached by middleware
    
    public function __construct()
    {
        $this->headers = getallheaders() ?: [];
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->query = $_GET;
        $this->post = $_POST;
    }
    
    /**
     * Get header value
     */
    public function header(string $name, $default = null)
    {
        return $this->headers[$name] ?? $default;
    }
    
    /**
     * Check if request has header
     */
    public function hasHeader(string $name): bool
    {
        return isset($this->headers[$name]);
    }
    
    /**
     * Set attribute (data to pass between middleware)
     */
    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }
    
    /**
     * Get attribute
     */
    public function getAttribute(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }
    
    /**
     * Check if request expects JSON response
     */
    public function expectsJson(): bool
    {
        $accept = $this->header('Accept', '');
        return str_contains($accept, 'application/json');
    }
    
    /**
     * Get bearer token from Authorization header
     */
    public function bearerToken(): ?string
    {
        $auth = $this->header('Authorization', '');
        if (str_starts_with($auth, 'Bearer ')) {
            return substr($auth, 7);
        }
        return null;
    }
}

/**
 * Middleware Interface
 * All middleware classes should implement this
 */
interface MiddlewareInterface
{
    /**
     * Handle the request
     * 
     * @param MiddlewareRequest $request
     * @param callable $next Next middleware in chain
     * @return mixed
     */
    public function handle(MiddlewareRequest $request, callable $next);
}
