<?php
// Metadata Demo View
?>
<div style="max-width: 900px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px;">
        SEO & Metadata API
    </h1>
    
    <p style="font-size: 1.1em; color: #666; margin-bottom: 30px;">
        NextPHP provides a Next.js-style Metadata API for SEO optimization and social sharing.
        Right-click and "View Page Source" to see the generated meta tags!
    </p>
    
    <!-- Code Examples -->
    <div style="display: grid; gap: 30px;">
        
        <!-- Basic Metadata -->
        <div style="background: #f8f9fa; padding: 25px; border-radius: 8px; border-left: 4px solid #3498db;">
            <h2 style="margin-top: 0; color: #2c3e50;">1. Basic Metadata</h2>
            <p>Set title, description, and keywords in your page controller:</p>
            <pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 4px; overflow-x: auto;"><code>&lt;?php
use NextPHP\Core\Metadata;

Metadata::set([
    'title' => 'My Page Title',
    'description' => 'A description for search engines',
    'keywords' => ['php', 'web', 'framework'],
]);</code></pre>
        </div>
        
        <!-- Dynamic Metadata -->
        <div style="background: #f8f9fa; padding: 25px; border-radius: 8px; border-left: 4px solid #27ae60;">
            <h2 style="margin-top: 0; color: #2c3e50;">2. Dynamic Metadata (like Next.js generateMetadata)</h2>
            <p>Generate metadata based on route parameters or database data:</p>
            <pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 4px; overflow-x: auto;"><code>&lt;?php
// In app/blog/[slug]/page.php
$slug = $slug ?? 'unknown';
$post = getPostFromDatabase($slug); // Your DB query

Metadata::set([
    'title' => $post['title'],
    'description' => $post['excerpt'],
    'canonical' => Metadata::getCurrentUrl(),
    'openGraph' => [
        'title' => $post['title'],
        'description' => $post['excerpt'],
        'type' => 'article',
        'publishedTime' => $post['created_at'],
        'authors' => [$post['author']],
    ],
]);</code></pre>
        </div>
        
        <!-- Title Templates -->
        <div style="background: #f8f9fa; padding: 25px; border-radius: 8px; border-left: 4px solid #e74c3c;">
            <h2 style="margin-top: 0; color: #2c3e50;">3. Title Templates</h2>
            <p>Use templates with %s placeholder for consistent titles:</p>
            <pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 4px; overflow-x: auto;"><code>&lt;?php
// In your layout
Metadata::set([
    'title' => [
        'template' => '%s | MyApp',
        'default' => 'MyApp',
    ],
]);

// In your page - only the page title
Metadata::set([
    'title' => 'Blog Post',  // Will become "Blog Post | MyApp"
]);</code></pre>
        </div>
        
        <!-- Open Graph -->
        <div style="background: #f8f9fa; padding: 25px; border-radius: 8px; border-left: 4px solid #9b59b6;">
            <h2 style="margin-top: 0; color: #2c3e50;">4. Open Graph & Twitter Cards</h2>
            <p>Add social sharing metadata:</p>
            <pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 4px; overflow-x: auto;"><code>&lt;?php
Metadata::set([
    'openGraph' => [
        'title' => 'My Page',
        'description' => 'Page description',
        'type' => 'website',
        'images' => [
            [
                'url' => 'https://example.com/og.jpg',
                'width' => 1200,
                'height' => 630,
                'alt' => 'OG Image',
            ],
        ],
    ],
    'twitter' => [
        'card' => 'summary_large_image',
        'site' => '@myhandle',
    ],
]);</code></pre>
        </div>
        
        <!-- Available Fields -->
        <div style="background: #fff3cd; padding: 25px; border-radius: 8px; border-left: 4px solid #ffc107;">
            <h2 style="margin-top: 0; color: #2c3e50;">5. All Available Metadata Fields</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="background: #f8f9fa;">
                    <th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Field</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Type</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Description</th>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;"><code>title</code></td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">string|array</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Page title with optional template</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;"><code>description</code></td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">string</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Meta description</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;"><code>keywords</code></td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">array|string</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Meta keywords</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;"><code>canonical</code></td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">string</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Canonical URL</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;"><code>robots</code></td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">array|string</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Robots directive</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;"><code>author</code></td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">string</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Page author</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;"><code>openGraph</code></td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">array</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Open Graph data</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;"><code>twitter</code></td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">array</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Twitter Card data</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;"><code>icons</code></td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">array</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Favicon and app icons</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;"><code>themeColor</code></td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">string|array</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Theme color for browser</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;"><code>css</code></td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">array</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Custom CSS files</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;"><code>js</code></td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">array</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Custom JavaScript files</td>
                </tr>
            </table>
        </div>
        
        <!-- How to Use -->
        <div style="background: #d4edda; padding: 25px; border-radius: 8px; border-left: 4px solid #28a745;">
            <h2 style="margin-top: 0; color: #2c3e50;">6. How to Use in Your Project</h2>
            <ol style="line-height: 1.8;">
                <li>
                    <strong>In your layout</strong> (<code>app/layout.php</code>), ensure it renders metadata:
                    <pre style="background: #2c3e50; color: #ecf0f1; padding: 10px; border-radius: 4px; margin: 10px 0;"><code>&lt;head&gt;
    &lt;?= Metadata::render() ?&gt;
&lt;/head&gt;</code></pre>
                </li>
                <li>
                    <strong>In your page</strong> (<code>app/page.php</code>), set metadata at the top:
                    <pre style="background: #2c3e50; color: #ecf0f1; padding: 10px; border-radius: 4px; margin: 10px 0;"><code>&lt;?php
use NextPHP\Core\Metadata;

Metadata::set([
    'title' => 'My Page',
    'description' => 'Page description',
]);</code></pre>
                </li>
                <li>
                    <strong>That's it!</strong> The metadata will automatically render in the head section.
                </li>
            </ol>
        </div>
        
        <!-- View Source Button -->
        <div style="text-align: center; padding: 20px;">
            <button onclick="document.documentElement.classList.toggle('show-source')" 
                    style="background: #3498db; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">
                📝 Tip: Right-click → "View Page Source" to see generated meta tags!
            </button>
        </div>
    </div>
</div>
