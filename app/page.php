<?php
// Home page controller
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
];

$demoLinks = [
    ['url' => '/blog/hello-world', 'text' => 'Blog Post [slug]'],
    ['url' => '/shop/clothes/tops', 'text' => 'Shop [...slug]'],
    ['url' => '/docs', 'text' => 'Docs Home [[...slug]]'],
    ['url' => '/docs/api/routing', 'text' => 'Docs Page [[...slug]]'],
    ['url' => '/file-demo', 'text' => 'FileHandler Demo'],
    ['url' => '/test-error', 'text' => 'Error Boundary Demo'],
];
