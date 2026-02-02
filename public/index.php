<?php
declare(strict_types=1);

// Register dynamic class loader first
require __DIR__ . "/../core/ClassLoader.php";
use NextPHP\Core\ClassLoader;
ClassLoader::register();

// Load core files that are always needed
require __DIR__ . "/../config.php";
require __DIR__ . "/../core/App.php";
require __DIR__ . "/../core/Router.php";
require __DIR__ . "/../core/Page.php";
require __DIR__ . "/../core/FileHandler.php";
require __DIR__ . "/../core/Metadata.php";
require __DIR__ . "/../core/Validator.php";
require __DIR__ . "/../core/Proxy.php";

// Proxies are now loaded dynamically via ClassLoader
// When Proxy::resolve('LoggerMiddleware') is called,
// it will auto-load from proxies/LoggerMiddleware.php

use NextPHP\Core\App;

$app = new App();
$app->run();
