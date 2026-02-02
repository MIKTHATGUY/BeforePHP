<?php
use NextPHP\Config;
use NextPHP\Core\Metadata;

// Get current metadata from the page (if any)
$currentMetadata = Metadata::get();

// Build base metadata if not already set
$baseTitle = Config::get('app.name', 'NextPHP');
$baseDescription = Config::get('app.description', 'A lightweight PHP framework inspired by Next.js');

// If page hasn't set title, use default
if (!isset($currentMetadata['title'])) {
    Metadata::set([
        'title' => $baseTitle,
        'description' => $baseDescription,
        'openGraph' => [
            'type' => 'website',
            'siteName' => $baseTitle,
        ],
    ]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= Metadata::render() ?>
</head>
<body>

    <h1><?= Config::get('app.name') ?></h1>
    <?= $page ?>

</body>
</html>