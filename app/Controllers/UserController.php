<?php

namespace App\Controllers;

class UserController extends HelperController
{
	public function passwordEdit($request, $response)
	{
		if ($this->userModel->validate($request, $this->userModel->newPasswordRules())->failed()) {
			return $this->formErrors();
		}
		
		
		return $this->success('Password updated');
	}
}