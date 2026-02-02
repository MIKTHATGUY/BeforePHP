<?php
declare(strict_types=1);

namespace NextPHP\Proxies;

use NextPHP\Core\ProxyRequest;
use NextPHP\Core\ProxyInterface;

/**
 * Request Logger Proxy
 * 
 * Logs all incoming requests for debugging and analytics
 */
class LoggerMiddleware implements ProxyInterface
{
    private string $logPath;
    private bool $logResponseTime;
    private bool $logPostData;
    private array $excludePaths;
    
    public function __construct(array $config = [])
    {
        $this->logPath = __DIR__ . '/../storage/logs';
        $this->logResponseTime = $config['log_response_time'] ?? true;
        $this->logPostData = $config['log_post_data'] ?? false;
        $this->excludePaths = $config['exclude_paths'] ?? ['/favicon.ico', '/robots.txt'];
        
        // Ensure log directory exists
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }
    
    public function handle(ProxyRequest $request, callable $next)
    {
        $startTime = microtime(true);
        
        // Check if path should be excluded
        $path = parse_url($request->uri, PHP_URL_PATH);
        if ($this->shouldExclude($path)) {
            return $next($request);
        }
        
        // Execute the request
        $response = $next($request);
        
        // Calculate response time
        $responseTime = $this->logResponseTime ? round((microtime(true) - $startTime) * 1000, 2) : null;
        
        // Build log entry
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $request->method,
            'uri' => $request->uri,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $request->header('User-Agent', 'unknown'),
            'referrer' => $request->header('Referer', '-'),
            'response_time_ms' => $responseTime,
            'status_code' => http_response_code() ?: 200,
        ];
        
        // Optionally log POST data
        if ($this->logPostData && !empty($request->post)) {
            $logEntry['post_data'] = $this->sanitizePostData($request->post);
        }
        
        // Write to log file
        $this->writeLog($logEntry);
        
        return $response;
    }
    
    private function shouldExclude(string $path): bool
    {
        foreach ($this->excludePaths as $excludePath) {
            if (str_starts_with($path, $excludePath)) {
                return true;
            }
        }
        return false;
    }
    
    private function sanitizePostData(array $data): array
    {
        $sensitiveFields = ['password', 'passwd', 'pwd', 'token', 'secret', 'credit_card', 'cvv'];
        
        $sanitized = [];
        foreach ($data as $key => $value) {
            $isSensitive = false;
            foreach ($sensitiveFields as $field) {
                if (stripos($key, $field) !== false) {
                    $isSensitive = true;
                    break;
                }
            }
            
            $sanitized[$key] = $isSensitive ? '***REDACTED***' : $value;
        }
        
        return $sanitized;
    }
    
    private function writeLog(array $entry): void
    {
        $date = date('Y-m-d');
        $logFile = $this->logPath . "/requests-{$date}.log";
        
        $logLine = json_encode($entry) . "\n";
        
        // Simple file append (no FileHandler dependency)
        $result = file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
        
        if ($result === false) {
            error_log("Failed to write request log to: " . $logFile);
        }
    }
}
