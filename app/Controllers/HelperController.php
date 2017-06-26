<?php

namespace App\Controllers;

class HelperController extends Controller
{
	public function errors($message = null)
	{
		return json_encode([
			'success' => false,
			'message' => $message
		]);
		
	}
	
	public function formErrors() {
		return json_encode([
			'success' => false,
			'message' => $_SESSION['errors']
		]);
	}
	
	public function success($message = null)
	{
		return json_encode([
			'success' => true,
			'message' => $message
		]);
	}
}