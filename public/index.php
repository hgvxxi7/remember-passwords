<?php

require_once __DIR__ . '/../vendor/autoload.php';

/* php -S localhost:8080 -t public public/index.php */

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$app = new Silex\Application();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../view'
));

$app->get("/", "PasswordManager\\Index\\Controller\\IndexController::indexAction");

$app['debug'] = true;
$app->run();