<?php
declare(strict_types=1);

use NextPHP\Config;

require __DIR__ . "/core/Config.php";

Config::set('app.name', 'Next-PHP');
Config::set('app.url', 'http://localhost:8080/next-php');

Config::set('paths.root', __DIR__);
Config::set('paths.core', __DIR__ . "/core");
Config::set('paths.pages', __DIR__ . "/pages");
Config::set('paths.public', __DIR__ . "/public");
