<?php
// Home page controller
use NextPHP\Core\Metadata;

// SEO Metadata configuration
Metadata::set([
    'title' => 'Home - NextPHP Framework',
    'description' => 'A powerful PHP framework inspired by Next.js. Build fast, SEO-friendly web applications with file-based routing, dynamic routes, and server-side rendering.',
    'keywords' => ['NextPHP', 'PHP framework', 'Next.js alternative', 'file-based routing', 'SSR', 'SEO'],
    'openGraph' => [
        'title' => 'NextPHP Framework',
        'description' => 'Build modern PHP applications with Next.js-style features',
        'type' => 'website',
        'url' => '/',
        'image' => '/og-image.png',
    ],
    'twitter' => [
        'card' => 'summary_large_image',
        'title' => 'NextPHP Framework',
        'description' => 'Build modern PHP applications with Next.js-style features',
        'image' => '/twitter-image.png',
    ],
]);
$features = [
    [
        'title' => 'File-Based Routing',
        'description' => 'Routes are automatically created from your file structure',
        'link' => null
    ],
    [
        'title' => 'Dynamic Routes',
        'description' => 'Support for [slug], [...slug], and [[...slug]] patterns',
        'link' => '/blog'
    ],
    [
        'title' => 'Controller + View',
        'description' => 'Separate logic (page.php) from presentation (page.html.php)',
        'link' => null
    ],
    [
        'title' => 'Layouts',
        'description' => 'Nested layouts with automatic inheritance',
        'link' => null
    ],
    [
        'title' => 'Error Boundaries',
        'description' => 'Catch errors at segment level with error.php',
        'link' => '/test-error'
    ],
    [
        'title' => 'FileHandler',
        'description' => 'Parse and save files with schema-based parsing',
        'link' => '/file-demo'
    ],
    [
        'title' => 'Query Strings',
        'description' => 'Access URL parameters automatically as variables',
        'link' => '/search'
    ],
    [
        'title' => 'POST Handling',
        'description' => 'Form submissions with automatic POST data parsing',
        'link' => '/contact'
    ],
    [
        'title' => 'SEO Metadata',
        'description' => 'Configure page metadata, Open Graph, and Twitter cards for optimal SEO',
        'link' => '/metadata-demo'
    ],
    [
        'title' => 'Auto-Validation',
        'description' => 'Automatic input validation with enable/disable toggle and flexible rules',
        'link' => '/register'
    ],
    [
        'title' => 'Middleware',
        'description' => 'Request filtering with auth, rate limiting, CORS, logging, and CSRF protection',
        'link' => '/middleware-demo'
    ],
];

$demoLinks = [
    ['url' => '/metadata-demo', 'text' => 'SEO Metadata Demo'],
    ['url' => '/register', 'text' => 'Auto-Validation Demo'],
    ['url' => '/middleware-demo', 'text' => 'Middleware System'],
    ['url' => '/blog/hello-world', 'text' => 'Blog Post [slug]'],
    ['url' => '/shop/clothes/tops', 'text' => 'Shop [...slug]'],
    ['url' => '/docs', 'text' => 'Docs Home [[...slug]]'],
    ['url' => '/docs/api/routing', 'text' => 'Docs Page [[...slug]]'],
    ['url' => '/file-demo', 'text' => 'FileHandler Demo'],
    ['url' => '/test-error', 'text' => 'Error Boundary Demo'],
    ['url' => '/search?q=php&category=tutorials', 'text' => 'Search (Query Strings)'],
];
