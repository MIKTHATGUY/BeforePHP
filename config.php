<?php
declare(strict_types=1);

use NextPHP\Config;

require __DIR__ . "/core/Config.php";

Config::set('app.name', 'NextPHP');
Config::set('app.url', 'http://localhost:8080/nextphp');

Config::set('paths.root', __DIR__);
Config::set('paths.core', __DIR__ . "/core");
Config::set('paths.app', __DIR__ . "/app");
Config::set('paths.public', __DIR__ . "/public");
