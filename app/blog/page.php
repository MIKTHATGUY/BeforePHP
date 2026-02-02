<?php
// Blog index controller
use NextPHP\Core\Metadata;
use NextPHP\Config;

// Set metadata for this page (static metadata example)
Metadata::set([
    'title' => [
        'template' => '%s - Blog',
        'default' => Config::get('app.name', 'NextPHP'),
    ],
    'description' => 'Read the latest articles about PHP development, web frameworks, and modern coding practices.',
    'keywords' => ['php', 'blog', 'tutorials', 'web development', 'nextphp'],
    'openGraph' => [
        'type' => 'website',
        'title' => 'Blog - NextPHP',
        'description' => 'Read the latest articles about PHP development and web frameworks.',
    ],
    'twitter' => [
        'card' => 'summary_large_image',
        'title' => 'Blog - NextPHP',
        'description' => 'Read the latest articles about PHP development.',
    ],
]);

$posts = [
    ['slug' => 'hello-world', 'title' => 'Hello World', 'excerpt' => 'Welcome to our blog!'],
    ['slug' => 'getting-started', 'title' => 'Getting Started', 'excerpt' => 'Learn the basics of NextPHP'],
    ['slug' => 'dynamic-routing', 'title' => 'Dynamic Routing', 'excerpt' => 'Understanding [slug] and [...slug] patterns'],
    ['slug' => 'metadata-guide', 'title' => 'SEO & Metadata Guide', 'excerpt' => 'Learn how to add SEO metadata to your pages'],
];
