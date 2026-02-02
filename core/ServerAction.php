<?php
declare(strict_types=1);

namespace NextPHP\Core;

/**
 * Server Actions
 * 
 * Handle form submissions directly in page controllers without separate API routes.
 * 
 * Usage:
 * 1. Create actions.php in your page folder
 * 2. Define functions prefixed with "action_"
 * 3. In form, add ?_action=functionName to URL
 * 
 * Example:
 * app/contact/actions.php:
 *   function action_submitForm($data) {
 *       // Process form
 *       return ['success' => true, 'message' => 'Sent!'];
 *   }
 * 
 * In page.html.php:
 *   <form method="POST" action="/contact?_action=submitForm">
 */
class ServerAction
{
    private static array $actions = [];
    private static array $registeredPaths = [];
    private static ?string $currentAction = null;
    private static ?string $currentPath = null;
    
    /**
     * Register actions from a file/path
     */
    public static function register(string $path): void
    {
        self::$registeredPaths[] = $path;
    }
    
    /**
     * Load actions from registered paths
     */
    public static function load(): void
    {
        foreach (self::$registeredPaths as $path) {
            $actionFile = $path . '/actions.php';
            if (file_exists($actionFile)) {
                // Include the file to load functions
                require_once $actionFile;
            }
        }
    }
    
    /**
     * Execute an action by name
     */
    public static function execute(string $actionName, array $data): array
    {
        $functionName = 'action_' . $actionName;
        
        if (!function_exists($functionName)) {
            return [
                'success' => false,
                'error' => "Action '{$actionName}' not found"
            ];
        }
        
        try {
            $result = $functionName($data);
            return [
                'success' => true,
                'data' => $result
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Handle action request if present
     * Returns response array or null if no action
     */
    public static function handleIfAction(array $get, array $post): ?array
    {
        // Check for action in GET or POST
        $action = $get['_action'] ?? $post['_action'] ?? null;
        
        if (!$action) {
            return null;
        }
        
        // Load all registered actions
        self::load();
        
        // Merge POST data with any other data
        $data = array_merge($get, $post);
        unset($data['_action']);
        
        // Execute the action
        return self::execute($action, $data);
    }
    
    /**
     * Get current executing action name
     */
    public static function getCurrentAction(): ?string
    {
        return self::$currentAction;
    }
    
    /**
     * Check if currently processing an action
     */
    public static function isProcessing(): bool
    {
        return self::$currentAction !== null;
    }
    
    /**
     * Create action form HTML
     */
    public static function form(string $action, array $options = []): string
    {
        $method = $options['method'] ?? 'POST';
        $csrf = self::csrfField();
        
        $html = "<form method=\"{$method}\" action=\"?action={$action}\"";
        
        if (isset($options['enctype'])) {
            $html .= " enctype=\"{$options['enctype']}\"";
        }
        
        if (isset($options['class'])) {
            $html .= " class=\"{$options['class']}\"";
        }
        
        $html .= ">";
        $html .= $csrf;
        
        return $html;
    }
    
    /**
     * Generate CSRF token for actions
     */
    public static function csrfField(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['_action_token'])) {
            $_SESSION['_action_token'] = bin2hex(random_bytes(32));
        }
        
        return '<input type="hidden" name="_action_token" value="' . $_SESSION['_action_token'] . '">';
    }
    
    /**
     * Validate CSRF token
     */
    public static function validateCsrf(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['_action_token'])) {
            return false;
        }
        
        return $token !== null && hash_equals($_SESSION['_action_token'], $token);
    }
    
    /**
     * Reset all registered actions (for testing)
     */
    public static function reset(): void
    {
        self::$actions = [];
        self::$registeredPaths = [];
        self::$currentAction = null;
        self::$currentPath = null;
    }
}

/**
 * Action Result Helper
 */
class ActionResult
{
    private bool $success;
    private ?string $message;
    private array $data;
    private ?string $redirect;
    
    public function __construct(bool $success, ?string $message = null, array $data = [], ?string $redirect = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
        $this->redirect = $redirect;
    }
    
    public static function success(?string $message = null, array $data = [], ?string $redirect = null): self
    {
        return new self(true, $message, $data, $redirect);
    }
    
    public static function error(string $message, array $data = []): self
    {
        return new self(false, $message, $data);
    }
    
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'redirect' => $this->redirect
        ];
    }
    
    public function json(): string
    {
        return json_encode($this->toArray());
    }
    
    public function isSuccess(): bool
    {
        return $this->success;
    }
    
    public function getMessage(): ?string
    {
        return $this->message;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
    
    public function getRedirect(): ?string
    {
        return $this->redirect;
    }
}
