<?php

namespace App\Controllers;

use App\Models\User;
use App\Validation\Validator;
use Slim\Http\Request;
use Slim\Views\Twig as View;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{
    public function getSignIn($request, $response)
    {
        return $this->view->render($response, 'signin.twig');
    }

    public function postSignIn($request, $response)
    {
        $auth = $this->auth->attempt(
            $request->getParam('email'),
            $request->getParam('password')
        );

        if (!$auth) {
            return json_encode([
                'success' => false
            ]);
        }

        return json_encode([
            'success' => true
        ]);
    }

    public function getSignUp($request, $response)
    {
        return $this->view->render($response, 'signup.twig');
    }

    public function postSignUp($request, $response)
    {
        if ($this->validateRequest($request)->failed()) {
            return json_encode([
                'success' => false
            ]);
        }

        $this->createUserTableIfNotExists();
        $data = (object)$request->getParams();
        $this->userCreate($data);

        return json_encode([
            'success' => true,
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

    private function validateRequest($request)
    {
        $validator = $this->validator->validate($request, [
            'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
            'name' => v::notEmpty()->alpha(),
            'password' => v::alnum()->noWhitespace()->length(4, 16),
        ]);

        return $validator;
    }

    private function userCreate($data)
    {
        $user = new User();
        $user->name = $data->name;
        $user->email = $data->email;
        $user->password = password_hash($data->password, PASSWORD_DEFAULT);
        $user->created_at = new \DateTime('now');
        $user->updated_at = null;
        $user->save();
    }
}