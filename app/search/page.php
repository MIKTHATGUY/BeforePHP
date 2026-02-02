<?php
// Search page controller - demonstrates query string usage

// Query string parameters are automatically available as variables
// Example: /search?q=php&category=tutorials&page=1

$searchQuery = $_q ?? '';                    // ?q=...
$category = $_category ?? 'all';             // ?category=...
$page = isset($_page) ? (int)$_page : 1;      // ?page=...
$page = max(1, $page);                      // Ensure page is at least 1

// Simulate search results based on query
$allResults = [
    ['title' => 'Getting Started with PHP', 'category' => 'tutorials', 'excerpt' => 'Learn the basics of PHP programming'],
    ['title' => 'Advanced Routing Techniques', 'category' => 'tutorials', 'excerpt' => 'Master dynamic routing in NextPHP'],
    ['title' => 'FileHandler Library Guide', 'category' => 'docs', 'excerpt' => 'Complete guide to file operations'],
    ['title' => 'Error Boundaries Explained', 'category' => 'docs', 'excerpt' => 'Understanding error handling'],
    ['title' => 'Query String Tutorial', 'category' => 'tutorials', 'excerpt' => 'Working with URL parameters'],
    ['title' => 'Configuration Best Practices', 'category' => 'docs', 'excerpt' => 'How to configure your app'],
];

// Filter results
$results = [];
if (!empty($searchQuery)) {
    foreach ($allResults as $result) {
        $matchesSearch = stripos($result['title'], $searchQuery) !== false || 
                         stripos($result['excerpt'], $searchQuery) !== false;
        $matchesCategory = $category === 'all' || $result['category'] === $category;
        
        if ($matchesSearch && $matchesCategory) {
            $results[] = $result;
        }
    }
}

$totalResults = count($results);
$resultsPerPage = 3;
$totalPages = ceil($totalResults / $resultsPerPage);
$page = min($page, max(1, $totalPages));

// Slice results for current page
$offset = ($page - 1) * $resultsPerPage;
$displayResults = array_slice($results, $offset, $resultsPerPage);

// Build current URL without query string for pagination links
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$baseUrl = $protocol . '://' . $host . $uri;
