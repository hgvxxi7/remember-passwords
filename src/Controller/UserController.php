<?php

namespace PasswordManager\Controller;

use PasswordManager\Form\Validate;
use PasswordManager\Model\UserModel;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class UserController
{
    protected $model;

    protected $validate;

    public function getModel()
    {
        if (!$this->model instanceof UserModel) { //instanceof - проверка элемента model на
            $this->model = new UserModel();       //содержание класса UserModel
        }
        return $this->model;
    }

    public function characterValidator()
    {
        if (!$this->validate instanceof Validate) { //instanceof - проверка элемента validate на
            $this->validate = new Validate();       //содержание класса validate
        }
        return $this->validate;
    }
    /*
     * Регистрация пользователя
     * Обработка и отображение формы региастрации и сохранение данных в БД
     * @param Request $request
     * @param Application $app
     * @return mixed
     */
    public function registrationAction(Request $request, Application $app)
    {
        $twigVariables = [];
        $twigVariables['registration'] = false;
        $twigVariables['userNameValidate'] = true;
        if ($request->getMethod() == 'POST')
        {
            $validate = $this->characterValidator();
            //$v = new validate();
            $twigVariables['userNameValidate'] = $validate->characterValidator($request->get('email'),6,20);
            if ($twigVariables['userNameValidate'] == true)
            {
                $model = $this->getModel();
                $model->registration($request->get('email'), $request->get('password'), $app['pdo']);
                $twigVariables['registration'] = true;
            }
        }
        $twigVariables['title'] = 'Registration form';
        return $app['twig']->render('registration.twig', $twigVariables);
    }
    /**
     * Метод используеться для login user
     * @param Request $request
     * @param Application $app
     * @return mixed
     */
    public function loginAction(Request $request, Application $app)
    {
        $twigVariables = [];
        $twigVariables['login'] = 0;
        if ($request->getMethod() == 'POST') {
            $model = $this->getModel();//ДЗ
            $result = $model->login($request->get('email'), $request->get('password'), $app['pdo']);
            /* if нужен для того-то-) */
            if (count($result) == 1) {
                $_SESSION['login'] = true;// определяем открываем сессию
                $twigVariables['login'] = 1;
            } else {
                $twigVariables['login'] = 2;
            }
        }
        $twigVariables['title'] = 'Log in form';
        return $app['twig']->render('login.twig', $twigVariables);
    }
    /* Добавить запрос SELECT. В запросе изменить условие на login = :login AND password = :password */

    public function usersAction(Request $request, Application $app)
    {
        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            return $app['twig']->render('access_deny.twig');
        }
        $twigVariables = [];
        /* @var $pdo \PDO */
        $pdo = $app['pdo'];
        /* SELECT запрос. */
        $stmt = $pdo->prepare('SELECT * FROM users');
        $stmt->execute();
        /* Получение результатов от mysql после выполнения SELECT */
        $result = $stmt->fetchAll();
        $twigVariables['result'] = $result;
        return $app['twig']->render('users.twig', $twigVariables);
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
