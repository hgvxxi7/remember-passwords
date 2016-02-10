<?php

namespace PasswordManager\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class UserController
{
    public function registrationAction(Request $request, Application $app)
    {
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
        }
        return $app['twig']->render('registration.twig', array(
            'title' => 'Registration form'
        ));
    }
}
