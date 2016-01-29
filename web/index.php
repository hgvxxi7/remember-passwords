<?php

require_once __DIR__.'/../vendor/autoload.php';

/* php -S localhost:8080 -t web web/index.php */

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
return false;
}

$app = new Silex\Application();
$app->run();