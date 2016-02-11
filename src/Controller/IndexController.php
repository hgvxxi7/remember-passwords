<?php

namespace PasswordManager\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class IndexController
{

    public function indexAction(Request $request, Application $app)
    {
        return $app['twig']->render('index.twig', array(
            'title' => 'Password Manager',
            'description' => 'Easy to remember, Easy to use, Impossible to hack'
        ));
    }
}
