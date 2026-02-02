<?php
declare(strict_types=1);

namespace NextPHP\Core;

/**
 * Proxy Manager (formerly Middleware)
 * 
 * Handles global, route-specific, and grouped proxy execution
 */
class Proxy
{
    private static array $globalProxies = [];
    private static array $routeProxies = [];
    private static array $proxyGroups = [];
    private static array $aliases = [];
    
    /**
     * Register global proxies (runs on all routes)
     */
    public static function global(array $proxies): void
    {
        self::$globalProxies = array_merge(self::$globalProxies, $proxies);
    }
    
    /**
     * Register route-specific proxies
     * 
     * @param string $route Route pattern (e.g., '/admin/*', '/api/*', '/user/[id]')
     * @param array $proxies Array of proxy class names or callables
     */
    public static function route(string $route, array $proxies): void
    {
        self::$routeProxies[$route] = array_merge(
            self::$routeProxies[$route] ?? [],
            $proxies
        );
    }
    
    /**
     * Register a proxy group
     * 
     * @param string $name Group name (e.g., 'web', 'api', 'admin')
     * @param array $proxies Array of proxy class names
     */
    public static function group(string $name, array $proxies): void
    {
        self::$proxyGroups[$name] = $proxies;
    }
    
    /**
     * Register proxy alias
     * 
     * @param string $alias Short name (e.g., 'auth', 'cors')
     * @param string $class Full class name
     */
    public static function alias(string $alias, string $class): void
    {
        self::$aliases[$alias] = $class;
    }
    
    /**
     * Resolve proxy from alias or class name
     */
    public static function resolve(string $proxy): ?object
    {
        // Check if it's an alias
        if (isset(self::$aliases[$proxy])) {
            $class = self::$aliases[$proxy];
        } else {
            $class = $proxy;
        }
        
        if (!class_exists($class)) {
            return null;
        }
        
        return new $class();
    }
    
    /**
     * Execute proxy chain
     * 
     * @param array $proxies Array of proxies to execute
     * @param callable $next The final handler (usually the page renderer)
     * @return mixed Response from proxy chain
     */
    public static function execute(array $proxies, callable $next)
    {
        // Build the proxy chain using array_reduce
        $chain = array_reduce(
            array_reverse($proxies),
            function ($nextProxy, $currentProxy) {
                return function ($request) use ($nextProxy, $currentProxy) {
                    return self::runProxy($currentProxy, $request, $nextProxy);
                };
            },
            $next
        );
        
        // Execute the chain with empty request object
        $request = new ProxyRequest();
        return $chain($request);
    }
    
    /**
     * Run a single proxy
     */
    private static function runProxy($proxy, $request, callable $next)
    {
        // If it's a string (class name or alias), resolve it
        if (is_string($proxy)) {
            $instance = self::resolve($proxy);
            if ($instance === null) {
                throw new \RuntimeException("Proxy not found: {$proxy}");
            }
            $proxy = $instance;
        }
        
        // If it's a callable (closure)
        if (is_callable($proxy)) {
            return $proxy($request, $next);
        }
        
        // If it's an object with handle method
        if (is_object($proxy) && method_exists($proxy, 'handle')) {
            return $proxy->handle($request, $next);
        }
        
        throw new \RuntimeException("Invalid proxy format");
    }
    
    /**
     * Get proxies for a specific route
     * 
     * @param string $uri Current request URI
     * @param string $routeName Route identifier (folder path)
     * @return array Combined proxies (global + route-specific + groups)
     */
    public static function getForRoute(string $uri, string $routeName): array
    {
        $proxies = self::$globalProxies;
        
        // Add route-specific proxies
        foreach (self::$routeProxies as $pattern => $routeProxies) {
            if (self::matchesRoute($pattern, $uri)) {
                $proxies = array_merge($proxies, $routeProxies);
            }
        }
        
        // Check for proxy groups based on route name
        if (str_starts_with($uri, '/api')) {
            $proxies = array_merge($proxies, self::$proxyGroups['api'] ?? []);
        }
        if (str_starts_with($uri, '/admin')) {
            $proxies = array_merge($proxies, self::$proxyGroups['admin'] ?? []);
        }
        
        return array_unique($proxies);
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
     * Reset all proxies (useful for testing)
     */
    public static function reset(): void
    {
        self::$globalProxies = [];
        self::$routeProxies = [];
        self::$proxyGroups = [];
        self::$aliases = [];
    }
    
    /**
     * Get all registered proxy info
     */
    public static function getAll(): array
    {
        return [
            'global' => self::$globalProxies,
            'route' => self::$routeProxies,
            'groups' => self::$proxyGroups,
            'aliases' => self::$aliases,
        ];
    }
}

/**
 * Proxy Request Object
 * Passed through proxy chain containing request data
 */
class ProxyRequest
{
    public array $headers = [];
    public array $params = [];
    public array $query = [];
    public array $post = [];
    public string $method = 'GET';
    public string $uri = '';
    public array $attributes = []; // Custom data attached by proxies
    
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
     * Set attribute (data to pass between proxies)
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
 * Proxy Interface
 * All proxy classes should implement this
 */
interface ProxyInterface
{
    /**
     * Handle the request
     * 
     * @param ProxyRequest $request
     * @param callable $next Next proxy in chain
     * @return mixed
     */
    public function handle(ProxyRequest $request, callable $next);
}
