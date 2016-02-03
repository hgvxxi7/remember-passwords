<?php

/* Change directory to project home */
chdir(dirname(__DIR__));
require_once 'vendor/autoload.php';

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$app = new Silex\Application();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../src/view'
));

$app->get("/", "PasswordManager\\Controller\\IndexController::indexAction");
$app->get("/{_locale}/registration", "PasswordManager\\Controller\\UserController::registrationAction");
$app->get("/registration", "PasswordManager\\Controller\\UserController::registrationAction")
    ->value('_locale', 'ru');

$app->register(new \Silex\Provider\TranslationServiceProvider());
$app->extend('translator', function (\Silex\Translator $translator, $app) {
    $translator->addLoader('array', new \Symfony\Component\Translation\Loader\PhpFileLoader());
    $translator->addResource('array', 'data/translation/ru.php', 'ru');
    return $translator;
});
$app['debug'] = true;
$app->run();