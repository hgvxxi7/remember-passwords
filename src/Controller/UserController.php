<?php

namespace PasswordManager\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class UserController
{
    public function registrationAction(Request $request, Application $app)
    {
        return $app['twig']->render('registration.twig', array(
            'title' => 'Registration form'
        ));
    }
}