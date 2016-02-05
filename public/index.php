<?php

/* Изменение директории на корень проекта, теперь все пути можно писать от корня проекта */
chdir(dirname(__DIR__));
require_once 'vendor/autoload.php';

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$app = new Silex\Application();
/**
 * Регистрация twig template engine
 * @see http://silex.sensiolabs.org/doc/providers/twig.html
 */
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => 'src/view'
));
/**
 * Регистрация ulr провайдера
 * @see http://silex.sensiolabs.org/doc/providers/url_generator.html
 */
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/**
 * Регистрация роутов
 * @see http://silex.sensiolabs.org/doc/usage.html#routing
 */
$app->get('/', 'PasswordManager\\Controller\\IndexController::indexAction')
    ->bind('home');
$app->get('/registration', 'PasswordManager\\Controller\\UserController::registrationAction')
    ->bind('registration');

$app['debug'] = true;
$app->run();