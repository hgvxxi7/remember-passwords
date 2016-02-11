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

    }
}
