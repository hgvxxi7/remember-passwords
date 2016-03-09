<?php

/* Изменение директории на корень проекта, теперь все пути можно писать от корня проекта */
chdir(dirname(__DIR__));
require_once 'vendor/autoload.php';

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}
/*запуск сессии и сохранение сессии*/
session_start();
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
 * Создание PDO объекта для работы с нашей БД
 * @see http://php.net/manual/en/class.pdo.php
 * Также здесь используется замыкание/анонимная функция- функция без имени
 * @see http://php.net/manual/en/functions.anonymous.php
 * $app->share() - это регистрация 1го экземпляра сервиса
 * @see http://silex.sensiolabs.org/doc/services.html#shared-services
 */
$app['pdo'] = $app->share(function () {
    return new PDO(
        'mysql:host=localhost;dbname=remember-password',
        'root',
        '123456'
    );
});

/**
 * Регистрация роутов
 * @see http://silex.sensiolabs.org/doc/usage.html#routing
 */
$app->get('/', 'PasswordManager\\Controller\\IndexController::indexAction')
/* bind привязывает уникальное имя к роуту. Чтобы данный роут можно вызвать из view по имени home. */
    ->bind('home');

/* GET метод для формы регистрации. получение формы */
$app->get('/registration', 'PasswordManager\\Controller\\UserController::registrationAction')
    ->bind('registration');
/* POST метод для формы регистрации. обработка полученных данных из формы */
$app->post('/registration', 'PasswordManager\\Controller\\UserController::registrationAction');

/* GET для страницы логина */
$app->get('/login', 'PasswordManager\\Controller\\UserController::loginAction')
    ->bind('login');
$app->post('/login', 'PasswordManager\\Controller\\UserController::loginAction');

/* GET для страницы юзеров */
$app->get('/users', 'PasswordManager\\Controller\\UserController::usersAction')
    ->bind('users');

/* GET для страницы юзера. Роут с переменной. http://silex.sensiolabs.org/doc/usage.html#route-variables */
$app->get('/user/{id}', 'PasswordManager\\Controller\\UserController::userAction')
/* В перменной id разрешаем использование только цыфр */
    ->assert('id', '\d+');

/**
 * Включает дебаг режим, для отображения информации об ошибках
 */
$app['debug'] = true;
$app->run();
