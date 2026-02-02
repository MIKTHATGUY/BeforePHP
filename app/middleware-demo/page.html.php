<?php
// Middleware Demo View
?>
<div style="max-width: 1000px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px;">
        Middleware System
    </h1>
    
    <p style="font-size: 1.1em; color: #666; margin-bottom: 30px;">
        Middleware provides a convenient mechanism for filtering HTTP requests and responses.
        Apply security, logging, rate limiting, and more to your routes.
    </p>

    <!-- What is Middleware -->
    <div style="background: #f8f9fa; padding: 25px; border-radius: 8px; margin-bottom: 30px;">
        <h2 style="margin-top: 0; color: #2c3e50;">What is Middleware?</h2>
        <p>
            Middleware acts as a bridge between a request and the application. It can:
        </p>
        <ul style="line-height: 1.8;">
            <li><strong>Authenticate users</strong> - Verify login before accessing protected routes</li>
            <li><strong>Rate limit requests</strong> - Prevent spam and abuse</li>
            <li><strong>Add CORS headers</strong> - Enable cross-origin requests</li>
            <li><strong>Log requests</strong> - Track traffic and analytics</li>
            <li><strong>Validate CSRF tokens</strong> - Prevent cross-site request forgery</li>
            <li><strong>Modify requests/responses</strong> - Add headers, transform data</li>
        </ul>
    </div>

    <!-- Code Examples -->
    <div style="display: grid; gap: 25px;">
        
        <!-- Basic Usage -->
        <div style="background: #e8f4f8; padding: 20px; border-radius: 8px; border-left: 4px solid #3498db;">
            <h3 style="margin-top: 0;">1. Registering Middleware</h3>
            <p>Configure middleware in <code>app/middleware/config.php</code>:</p>
            <pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 4px; overflow-x: auto;"><code>&lt;?php
use NextPHP\Core\Middleware;
use NextPHP\App\Middleware\AuthMiddleware;

// Define aliases for middleware
Middleware::alias('auth', AuthMiddleware::class);

// Global middleware (runs on ALL routes)
Middleware::global(['logger']);

// Route-specific middleware
Middleware::route('/admin/*', ['auth']);
Middleware::route('/api/*', ['rateLimit', 'cors']);
Middleware::route('/contact', ['csrf']);

// Middleware groups
Middleware::group('api', ['rateLimit', 'cors']);
Middleware::group('admin', ['auth', 'logger']);</code></pre>
        </div>

        <!-- Creating Middleware -->
        <div style="background: #fff3cd; padding: 20px; border-radius: 8px; border-left: 4px solid #ffc107;">
            <h3 style="margin-top: 0;">2. Creating Custom Middleware</h3>
            <p>Create a class implementing <code>MiddlewareInterface</code>:</p>
            <pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 4px; overflow-x: auto;"><code>&lt;?php
namespace NextPHP\App\Middleware;

use NextPHP\Core\MiddlewareRequest;
use NextPHP\Core\MiddlewareInterface;

class CheckAgeMiddleware implements MiddlewareInterface
{
    public function handle(MiddlewareRequest $request, callable $next)
    {
        $age = $_age ?? 0;
        
        if ($age < 18) {
            http_response_code(403);
            return 'You must be 18+ to access this page';
        }
        
        // Pass to next middleware/page
        return $next($request);
    }
}</code></pre>
        </div>

        <!-- Available Middleware -->
        <div style="background: #f0f0f0; padding: 20px; border-radius: 8px;">
            <h3 style="margin-top: 0;">3. Built-in Middleware</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9em;">
                <tr style="background: #e0e0e0;">
                    <th style="padding: 10px; text-align: left; border: 1px solid #ccc;">Middleware</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #ccc;">Purpose</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #ccc;">Usage Example</th>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ccc;"><code>AuthMiddleware</code></td>
                    <td style="padding: 10px; border: 1px solid #ccc;">User authentication</td>
                    <td style="padding: 10px; border: 1px solid #ccc;"><code>Middleware::route('/admin/*', ['auth']);</code></td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ccc;"><code>RateLimitMiddleware</code></td>
                    <td style="padding: 10px; border: 1px solid #ccc;">Request throttling</td>
                    <td style="padding: 10px; border: 1px solid #ccc;"><code>new RateLimitMiddleware(60, 60)</code></td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ccc;"><code>CORSMiddleware</code></td>
                    <td style="padding: 10px; border: 1px solid #ccc;">Cross-origin headers</td>
                    <td style="padding: 10px; border: 1px solid #ccc;"><code>new CORSMiddleware(['allowed_origins' => ['*']])</code></td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ccc;"><code>LoggerMiddleware</code></td>
                    <td style="padding: 10px; border: 1px solid #ccc;">Request logging</td>
                    <td style="padding: 10px; border: 1px solid #ccc;"><code>Middleware::global(['logger']);</code></td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ccc;"><code>CSRFMiddleware</code></td>
                    <td style="padding: 10px; border: 1px solid #ccc;">CSRF protection</td>
                    <td style="padding: 10px; border: 1px solid #ccc;"><code>Middleware::route('/forms/*', ['csrf']);</code></td>
                </tr>
            </table>
        </div>

        <!-- Middleware Chain -->
        <div style="background: #d4edda; padding: 20px; border-radius: 8px; border-left: 4px solid #28a745;">
            <h3 style="margin-top: 0;">4. How Middleware Chain Works</h3>
            <pre style="background: white; padding: 15px; border-radius: 4px; border: 2px solid #28a745;">
Request → Middleware 1 → Middleware 2 → Middleware 3 → Page
            ↓               ↓               ↓
         Check Auth    Rate Limit    CSRF Token
         (pass/fail)   (pass/fail)   (pass/fail)
                                            ↓
                                     Response ← Middleware 3 ← Middleware 2 ← Middleware 1
            </pre>
            <p style="margin-bottom: 0;">
                Middleware executes in order. If any middleware fails (returns early), 
                the chain stops and the response is returned immediately.
            </p>
        </div>

        <!-- Rate Limiting Example -->
        <div style="background: #f8d7da; padding: 20px; border-radius: 8px; border-left: 4px solid #dc3545;">
            <h3 style="margin-top: 0;">5. Rate Limiting Example</h3>
            <pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 4px; overflow-x: auto;"><code>// Allow 5 requests per hour to contact form
Middleware::route('/contact', [
    new RateLimitMiddleware(5, 3600) // 5 requests, 3600 seconds (1 hour)
]);

// Allow 100 API requests per minute
Middleware::route('/api/*', [
    new RateLimitMiddleware(100, 60)
]);

// Response headers when rate limited:
// X-RateLimit-Limit: 100
// X-RateLimit-Remaining: 0
// Retry-After: 45</code></pre>
        </div>

        <!-- CSRF Protection -->
        <div style="background: #e8f4f8; padding: 20px; border-radius: 8px; border-left: 4px solid #17a2b8;">
            <h3 style="margin-top: 0;">6. CSRF Protection for Forms</h3>
            <p>CSRF middleware automatically validates tokens on POST requests:</p>
            <pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 4px; overflow-x: auto;"><code>// In config.php - protect specific routes
Middleware::route('/contact', ['csrf']);
Middleware::route('/login', ['csrf']);

// In your form - add the token
&lt;form method="POST" action="/contact"&gt;
    &lt;?= csrf_field() ?&gt;
    &lt;!-- or manually --&gt;
    &lt;input type="hidden" name="_csrf_token" value="&lt;?= csrf_token() ?&gt;"&gt;
    &lt;!-- form fields --&gt;
&lt;/form&gt;

// For AJAX requests, include in header:
headers: {
    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
}</code></pre>
        </div>

        <!-- Request Object -->
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #6c757d;">
            <h3 style="margin-top: 0;">7. Middleware Request Object</h3>
            <p>Access request data in your middleware:</p>
            <pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 4px; overflow-x: auto;"><code>public function handle(MiddlewareRequest $request, callable $next)
{
    // Get headers
    $userAgent = $request->header('User-Agent');
    $authToken = $request->header('Authorization');
    
    // Check if request expects JSON
    if ($request->expectsJson()) {
        // Return JSON response
    }
    
    // Get bearer token
    $token = $request->bearerToken();
    
    // Set custom attributes to pass to next middleware
    $request->setAttribute('user_id', 123);
    $userId = $request->getAttribute('user_id');
    
    return $next($request);
}</code></pre>
        </div>
    </div>

    <!-- Quick Reference -->
    <div style="margin-top: 30px; padding: 20px; background: #fff; border: 2px solid #3498db; border-radius: 8px;">
        <h3 style="margin-top: 0; color: #3498db;">Quick Reference</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <h4>Register Middleware</h4>
                <code style="display: block; background: #f4f4f4; padding: 10px; border-radius: 4px;">
                    Middleware::route('/path', ['auth']);<br>
                    Middleware::global(['logger']);<br>
                    Middleware::group('api', ['cors']);
                </code>
            </div>
            <div>
                <h4>Custom Middleware</h4>
                <code style="display: block; background: #f4f4f4; padding: 10px; border-radius: 4px;">
                    class MyMiddleware implements MiddlewareInterface {<br>
                    &nbsp;&nbsp;public function handle($request, $next) {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;return $next($request);<br>
                    &nbsp;&nbsp;}<br>
                    }
                </code>
            </div>
        </div>
    </div>

    <!-- Demo Notice -->
    <div style="margin-top: 30px; padding: 15px; background: #d4edda; border-radius: 4px; text-align: center;">
        <strong>Try it:</strong> This page demonstrates the middleware system. 
        Check the code in <code>app/middleware-demo/</code> and <code>app/middleware/config.php</code>
    </div>
</div>
