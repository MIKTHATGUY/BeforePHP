<?php
use NextPHP\Config;
use NextPHP\Core\Metadata;

// Get current metadata or set defaults
$currentMetadata = Metadata::get();
$baseTitle = Config::get('app.name', 'NextPHP');

if (!isset($currentMetadata['title'])) {
    Metadata::set([
        'title' => 'Error - ' . $baseTitle,
        'description' => 'An error occurred',
        'robots' => 'noindex',
    ]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= Metadata::render() ?>
</head>
<body>

    <h1>Error</h1>
    <?= $page ?>

</body>
</html>