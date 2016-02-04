<?php

/* Change directory to project folder */
chdir(dirname(__DIR__));
require_once 'vendor/autoload.php';

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$app = new Silex\Application();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => 'src/view'
));
$app->register(new \Silex\Provider\TranslationServiceProvider());
$app->extend('translator', function (\Silex\Translator $translator, $app) {
    $translator->addLoader('array', new \Symfony\Component\Translation\Loader\PhpFileLoader());
    $translator->addResource('array', 'data/translation/ru.php', 'ru');
    $translator->addResource('array', 'data/translation/en.php', 'en');
    return $translator;
});
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->get('/{_locale}', 'PasswordManager\\Controller\\IndexController::indexAction')
    ->value('_locale', 'en')
    ->bind('home');
$app->get('/{_locale}/registration', 'PasswordManager\\Controller\\UserController::registrationAction')
    ->bind('registration');

$app['debug'] = true;
$app->run();