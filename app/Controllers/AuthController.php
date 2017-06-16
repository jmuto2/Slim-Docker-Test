<?php

namespace App\Controllers;

use App\Models\User;
use App\Validation\Validator;
use Slim\Http\Request;
use Slim\Views\Twig as View;
use Respect\Validation\Validator as v;

class AuthController extends HelperController
{
    public function getSignOut($request, $response)
    {
        $this->auth->logout();

        return $response->withRedirect($this->router->pathFor('auth.getsignin'));
    }
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
            return $this->errors();
        }

        return $this->success();
    }

    public function getSignUp($request, $response)
    {
        $_SESSION['user'] = null;
        return $this->view->render($response, 'signup.twig');
    }

    public function postSignUp($request, $response)
    {
        $user = new User();

        if ($user->validate($request, $user->rules())->failed()) {
            return $this->errors();
        }
        $user->add($request);

        return $this->success();
    }
}