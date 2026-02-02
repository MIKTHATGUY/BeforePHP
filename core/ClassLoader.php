<?php
declare(strict_types=1);

namespace NextPHP\Core;

/**
 * Dynamic Class Loader for Proxies and other classes
 * 
 * Loads classes on demand instead of requiring all files upfront
 */
class ClassLoader
{
    private static array $namespaces = [];
    private static bool $registered = false;
    
    /**
     * Register the autoloader
     */
    public static function register(): void
    {
        if (self::$registered) {
            return;
        }
        
        spl_autoload_register([self::class, 'load']);
        self::$registered = true;
        
        // Register default namespaces
        self::addNamespace('NextPHP\\Core\\', __DIR__ . '/');
        self::addNamespace('NextPHP\\Proxies\\', __DIR__ . '/../proxies/');
        self::addNamespace('NextPHP\\', __DIR__ . '/../core/');
    }
    
    /**
     * Add a namespace mapping
     * 
     * @param string $namespace The namespace prefix (e.g., 'NextPHP\\Proxies\\')
     * @param string $path The directory path
     */
    public static function addNamespace(string $namespace, string $path): void
    {
        self::$namespaces[$namespace] = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
    }
    
    /**
     * Autoload callback
     */
    public static function load(string $class): void
    {
        // Try to find the class in registered namespaces
        foreach (self::$namespaces as $namespace => $path) {
            if (str_starts_with($class, $namespace)) {
                // Remove namespace prefix to get relative class path
                $relativeClass = substr($class, strlen($namespace));
                
                // Convert namespace separators to directory separators
                $file = $path . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
                
                if (file_exists($file)) {
                    require $file;
                    return;
                }
            }
        }
    }
    
    /**
     * Load a specific class file directly
     */
    public static function loadClass(string $class): bool
    {
        foreach (self::$namespaces as $namespace => $path) {
            if (str_starts_with($class, $namespace)) {
                $relativeClass = substr($class, strlen($namespace));
                $file = $path . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
                
                if (file_exists($file)) {
                    require $file;
                    return true;
                }
            }
        }
        
        return false;
    }
}
