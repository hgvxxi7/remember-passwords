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
        $twigVariables = [];
        $twigVariables['login'] = 0;
        if ($request->getMethod() == 'POST') {
            $pdo = $app['pdo'];
            $stmt = $pdo->prepare('SELECT * FROM users WHERE login = :login and password = MD5(CONCAT(:password , users.salt))');
            $stmt->bindParam(':login', $request->get('email'));
            $stmt->bindParam(':password', $request->get('password'));
            $stmt->execute();
            $result = $stmt->fetchAll();
            if(count($result) == 1){
                $twigVariables['login'] = 1;
            }
            else {
                $twigVariables['login'] = 2;
            }
        }
        $twigVariables['title'] = 'Log in form';

        return $app['twig']->render('login.twig', $twigVariables);

        /* Добавить запрос SELECT. В запросе изменить условие на login = :login AND password = :password */
    }

    public function usersAction(Request $request, Application $app)
    {
        $twigVariables = [];
        /* @var $pdo \PDO */
        $pdo = $app['pdo'];
        /* SELECT запрос. */
        $stmt = $pdo->prepare('SELECT * FROM users');
        $stmt->execute();
        /* Получение результатов от mysql после выполнения SELECT */
        $result = $stmt->fetchAll();

        $twigVariables['result'] = $result;

        return $app['twig']->render('users.twig',$twigVariables);
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
