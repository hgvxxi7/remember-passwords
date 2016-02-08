<?php

namespace PasswordManager\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class UserController
{
    public function registrationAction(Request $request, Application $app)
    {
        if ($request->getMethod() == 'POST') {
            /* @var $pdo \PDO */
            $pdo = $app['pdo'];
            $stmt = $pdo->prepare('INSERT INTO users (login, password, salt) VALUES (:login, :password, :salt)');
            $stmt->bindParam(':login', $request->get('email'));
            $stmt->bindParam(':password', $request->get('password'));
            $stmt->bindParam(':salt', $request->get('password'));
            $stmt->execute();
        }
        return $app['twig']->render('registration.twig', array(
            'title' => 'Registration form'
        ));
    }
}
