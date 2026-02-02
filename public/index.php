<?php
declare(strict_types=1);

require __DIR__ . "/../config.php";
require __DIR__ . "/../core/App.php";
require __DIR__ . "/../core/Router.php";
require __DIR__ . "/../core/Page.php";
require __DIR__ . "/../core/FileHandler.php";

use NextPHP\Core\App;

$app = new App();
$app->run();
