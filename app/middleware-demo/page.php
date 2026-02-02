<?php
// Middleware Demo Controller
use NextPHP\Core\Metadata;
use NextPHP\Core\MiddlewareRequest;

// Set page metadata
Metadata::set([
    'title' => 'Middleware System Demo',
    'description' => 'Demonstrates the NextPHP middleware system with examples',
]);

// In a real middleware, you could access the request object:
// $request->header('User-Agent')
// $request->bearerToken()
// $request->expectsJson()

// Simulate middleware data that would be passed through
$middlewareInfo = [
    'Executed Middleware' => [
        'LoggerMiddleware' => 'Logs all requests (currently disabled in config)',
        'CORSMiddleware' => 'Adds CORS headers (currently disabled in config)',
        'RateLimitMiddleware' => 'Rate limiting for forms',
    ],
    'Available Middleware' => [
        'AuthMiddleware' => 'Authentication & authorization checks',
        'RateLimitMiddleware' => 'Request rate limiting',
        'CORSMiddleware' => 'Cross-Origin Resource Sharing headers',
        'LoggerMiddleware' => 'Request logging & analytics',
        'CSRFMiddleware' => 'CSRF token protection for forms',
    ],
];

// Check if this page has any middleware applied
$currentUri = $_SERVER['REQUEST_URI'] ?? '/';
$hasMiddleware = false; // Will be set based on actual middleware config

// Demo: Show how to access request data that middleware would provide
$demoRequestData = [
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
    'uri' => $currentUri,
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
    'headers' => getallheaders() ?: [],
];
