<?php
// Metadata Demo - Comprehensive SEO & Metadata Example
use NextPHP\Core\Metadata;
use NextPHP\Config;

// Example 1: Static Metadata (like Next.js export const metadata)
Metadata::set([
    // Basic SEO
    'title' => 'SEO & Metadata Guide',
    'description' => 'Learn how to add SEO metadata to your NextPHP pages with Open Graph, Twitter Cards, and more.',
    'keywords' => ['seo', 'metadata', 'open graph', 'twitter cards', 'nextphp'],
    
    // Canonical URL
    'canonical' => Metadata::getCurrentUrl(),
    
    // Robots directive
    'robots' => ['index', 'follow'],
    
    // Author
    'authors' => [
        ['name' => 'NextPHP Team', 'url' => 'https://github.com/nextphp'],
    ],
    
    // Open Graph (for Facebook, LinkedIn, etc.)
    'openGraph' => [
        'title' => 'SEO & Metadata Guide - NextPHP',
        'description' => 'Learn how to add comprehensive SEO metadata to your pages.',
        'type' => 'article',
        'url' => Metadata::getCurrentUrl(),
        'siteName' => Config::get('app.name', 'NextPHP'),
        'locale' => 'en_US',
        'images' => [
            [
                'url' => 'https://example.com/og-image.jpg',
                'width' => 1200,
                'height' => 630,
                'alt' => 'NextPHP SEO Guide',
            ],
        ],
    ],
    
    // Twitter Card
    'twitter' => [
        'card' => 'summary_large_image',
        'title' => 'SEO & Metadata Guide - NextPHP',
        'description' => 'Learn how to add comprehensive SEO metadata to your pages.',
        'site' => '@nextphp',
        'creator' => '@nextphp',
        'images' => [
            [
                'url' => 'https://example.com/twitter-image.jpg',
                'alt' => 'NextPHP SEO Guide',
            ],
        ],
    ],
    
    // Icons
    'icons' => [
        'icon' => '/favicon.ico',
        'shortcut' => '/favicon.ico',
        'apple' => '/apple-touch-icon.png',
        'other' => [
            [
                'rel' => 'icon',
                'url' => '/icon-192.png',
                'sizes' => '192x192',
            ],
            [
                'rel' => 'icon',
                'url' => '/icon-512.png',
                'sizes' => '512x512',
            ],
        ],
    ],
    
    // Theme color
    'themeColor' => [
        'color' => '#3498db',
        'media' => '(prefers-color-scheme: light)',
    ],
    
    // Custom CSS/JS
    'css' => ['/css/seo-demo.css'],
    'js' => ['/js/analytics.js'],
    
    // Custom meta tags
    'other' => [
        ['name' => 'generator', 'content' => 'NextPHP'],
        ['property' => 'custom:property', 'content' => 'Custom Value'],
    ],
]);

// Get current metadata for display
$currentMetadata = Metadata::get();
