<?php
// Blog post controller with dynamic metadata
use NextPHP\Core\Metadata;

$slug = $_slug ?? 'unknown';
$postTitle = ucwords(str_replace('-', ' ', $slug));
$postContent = "This is the content for blog post: {$postTitle}. The slug parameter is: {$slug}";

// Simulate fetching post data (in real app, this would come from database)
$postData = [
    'title' => $postTitle,
    'description' => "Read our article about {$postTitle}. Learn more about this topic.",
    'author' => 'NextPHP Team',
    'published' => date('Y-m-d'),
    'tags' => ['php', 'tutorial', $slug],
];

// Generate dynamic metadata (like Next.js generateMetadata function)
Metadata::set([
    'title' => $postData['title'],
    'description' => $postData['description'],
    'author' => $postData['author'],
    'keywords' => $postData['tags'],
    'canonical' => Metadata::getCurrentUrl(),
    'robots' => ['index', 'follow'],
    'openGraph' => [
        'title' => $postData['title'],
        'description' => $postData['description'],
        'type' => 'article',
        'publishedTime' => $postData['published'],
        'authors' => [$postData['author']],
    ],
    'twitter' => [
        'card' => 'summary_large_image',
        'title' => $postData['title'],
        'description' => $postData['description'],
    ],
]);
