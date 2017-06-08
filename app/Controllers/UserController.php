<?php

namespace App\Controllers;

class UserController extends Controller
{
    public function add($request, $response)
    {
        $email = 'jmuto2@gmail.coms';
        $sql = "
          SELECT * 
          FROM users 
          WHERE email = :id
        ";
        $stm = $this->db->prepare($sql);
        $bind = [
            ':id' => $email
        ];
        $stm->execute($bind);
        $user = $stm->fetch();

        if ($user) {
            return json_encode([
                'error' => 'User exists alread for this email'
            ]);
        }

        $name = 'Larry Johnson';
        $email = 'lj@gmail.com';
        $password = 'secret';
        $sql = "
          INSERT INTO users 
          (name, email, password, created_at)
          VALUES (:name, :email, :password, CURRENT_TIMESTAMP)
        ";
        $stm = $this->db->prepare($sql);
        $bind = [
            ':name' => $name,
            ':email' => $email,
            ':password' =>  password_hash($password, PASSWORD_BCRYPT)
        ];
        $saved = $stm->execute($bind);

        if ($saved) {
            echo 'Saved!';
        }
        die;
    }
}