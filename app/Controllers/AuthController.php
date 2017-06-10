<?php

namespace App\Controllers;

use Slim\Views\Twig as View;

class AuthController extends Controller
{
    public function getSignUp($request, $response)
    {
        return $this->view->render($response, 'signup.twig');
    }

    public function postSignUp($request, $response)
    {
        $this->createUserTableIfNotExists();

        $data = (object)$request->getParams();

        $sql = "
          SELECT * 
          FROM users 
          WHERE email = :email
        ";
        $stm = $this->db->prepare($sql);
        $bind = [
            ':email' => $data->email
        ];
        $stm->execute($bind);
        $user = $stm->fetch();

        if ($user) {
            return json_encode([
                'error' => 'User exists already for this email'
            ]);
        }

        $sql = "
          INSERT INTO users 
          (name, email, password, created_at)
          VALUES (:name, :email, :password, CURRENT_TIMESTAMP)
        ";
        $stm = $this->db->prepare($sql);
        $bind = [
            ':name' => $data->name,
            ':email' => $data->email,
            ':password' =>  password_hash($data->password, PASSWORD_BCRYPT)
        ];
        $stm->execute($bind);

        //return $response->withRedirect($this->router->path_for('home'));
        return json_encode([
            'success' => true,
            'message' => 'User signed up'
        ]);
    }

    private function createUserTableIfNotExists()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                name VARCHAR(255) NOT NULL,
                email VARCHAR(30) NOT NULL,
                password VARCHAR(50) NOT NULL,
                created_at DATETIME,
                updated_at DATETIME
        )";
        $stm = $this->db->prepare($sql);
        $stm->execute();
    }
}