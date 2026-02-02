<?php
// Docs optional catch-all controller
// Access slugs via $slug variable - returns array or null/undefined

$slugs = $slug ?? null;
$hasSlugs = $slugs !== null && is_array($slugs) && !empty($slugs);
$pageTitle = $hasSlugs ? implode(' / ', $slugs) : 'Documentation Home';
