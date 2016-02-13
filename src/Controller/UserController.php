<?php

namespace PasswordManager\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class UserController
{
    /**
     * Регистрация пользователя
     * Обработка и отображение формы региастрации и сохранение данных в БД
     *
     * @param Request $request
     * @param Application $app
     * @return mixed
     */
    public function registrationAction(Request $request, Application $app)
    {
        $twigVariables = [];
        $twigVariables['registration'] = false;

        if ($request->getMethod() == 'POST') {

            $salt = md5(microtime());
            $password = md5($request->get('password').$salt);

            /* @var $pdo \PDO */
            $pdo = $app['pdo'];
            $stmt = $pdo->prepare('INSERT INTO users (login, password, salt) VALUES (:login, :password, :salt)');
            $stmt->bindParam(':login', $request->get('email'));
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':salt', $salt);
            $stmt->execute();
            $twigVariables['registration'] = true;
        }

        $twigVariables['title'] = 'Registration form';
        return $app['twig']->render('registration.twig', $twigVariables);
    }

    public function loginAction(Request $request, Application $app)
    {
        /* Добавить запрос SELECT. В запросе изменить условие на login = :login AND password = :password */
    }

    public function usersAction(Request $request, Application $app)
    {
        /* @var $pdo \PDO */
        $pdo = $app['pdo'];
        /* SELECT запрос. */
        /* Сверять пароль нужно закодированный по условию которое описанно в методе регистрации */
        $stmt = $pdo->prepare('SELECT * FROM users');
        $stmt->execute();
        /* Получение результатов от mysql после выполнения SELECT */
        $result = $stmt->fetchAll();

        /* Нужно выводить данные во вью */
        var_dump($result); die;
    }

    /**
     * Получение данных для указанного юзера
     * @param Request $request
     * @param Application $app
     */
    public function userAction(Request $request, Application $app)
    {
        /* @var $pdo \PDO */
        $pdo = $app['pdo'];

        /* SELECT запрос с простым условием id=:id. */
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
        /* Подставляем переменную id из GET параметров */
        $stmt->bindParam(':id', $request->get('id'));
        /* Выполнение Sql запроса */
        $stmt->execute();
        /* Получение 1го результата от mysql после выполнения SELECT */
        $result = $stmt->fetch();

        /* Передача данных во вью */
        return $app['twig']->render(
            'user.twig',
            [
                'id' => $result['id'],
                'login' => $result['login'],
                'password' => $result['password'],
                'salt' => $result['salt'],
                'dt_created' => $result['dt_created']
            ]
        );
    }
}
