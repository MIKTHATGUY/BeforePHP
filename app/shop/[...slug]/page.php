<?php
// Shop catch-all controller
// Access slugs via $slug variable - returns array

$slugs = $slug ?? [];
$categoryPath = is_array($slugs) ? implode(' / ', $slugs) : $slugs;
$categoryTitle = is_array($slugs) && !empty($slugs) ? end($slugs) : 'Shop';
