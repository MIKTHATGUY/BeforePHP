<?php
// Blog post controller
// Access slug via $slug variable (extracted from params)

$slug = $slug ?? 'unknown';
$postTitle = ucwords(str_replace('-', ' ', $slug));
$postContent = "This is the content for blog post: {$postTitle}. The slug parameter is: {$slug}";
