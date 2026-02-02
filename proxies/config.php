<?php
declare(strict_types=1);

/**
 * Proxy Configuration
 * 
 * Simple setup for logging page visits
 */

use NextPHP\Core\Proxy;
use NextPHP\Proxies\LoggerMiddleware;

// Enable request logging globally
// This logs all page visits to storage/logs/requests-YYYY-MM-DD.log
Proxy::global([
    new LoggerMiddleware([
        'log_response_time' => true,  // Log how long each page takes to render
        'log_post_data' => false,     // Don't log POST data (privacy/security)
        'exclude_paths' => ['/favicon.ico', '/robots.txt'],  // Don't log these
    ])
]);

// The logger will record:
// - Timestamp
// - HTTP method (GET, POST, etc.)
// - Request URI
// - IP address
// - User agent
// - Response time (in milliseconds)
// - Status code
