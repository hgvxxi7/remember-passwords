<?php
/**
 * Created by PhpStorm.
 * User: artemas
 * Date: 2/24/16
 * Time: 8:10 PM
 */

namespace PasswordManager\Model;


class UserModel
{
    public function login($email,$password, $pdo)
    {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE login = :login and password = MD5(CONCAT(:password , users.salt))');
        $stmt->bindParam(':login', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    public function registration($email, $password, $pdo)
    {
        $salt = md5(microtime());
        $password = md5($password . $salt);
        $stmt = $pdo->prepare('INSERT INTO users (login, password, salt) VALUES (:login, :password, :salt)');
        $stmt->bindParam(':login', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':salt', $salt);
        $stmt->execute();
    }
}