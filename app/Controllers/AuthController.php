<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Views\Twig as View;

class AuthController extends HelperController
{
	public function index($request, $response)
	{
		return $this->view->render($response, 'index.twig');
	}
	
	public function getSignOut($request, $response)
	{
		$this->auth->logout();
		
		return $response->withRedirect($this->router->pathFor('auth.index'));
	}
	
	public function getSignIn($request, $response)
	{
		return $this->view->render($response, 'signin.twig');
	}
	
	public function postSignIn($request, $response)
	{
		if ($this->userModel->validate($request, $this->userModel->signinRules())->failed()) {
			return $this->errors();
		}
		
		$auth = $this->auth->attempt(
			$request->getParam('email'),
			$request->getParam('password')
		);
		
		if (!$auth) {
			return $this->errors('Please check your credentials and try again');
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
		if ($this->userModel->validate($request, $this->userModel->rules())->failed()) {
			return $this->errors();
		}
		$this->userModel->add($request);
		
		return $this->success();
	}
}