<?php

namespace App\Controllers;

class HelperController extends Controller
{
    public function errors()
    {
        return json_encode([
            'success' => false
        ]);

    }

    public function success()
    {
        return json_encode([
            'success' => true
        ]);
    }
}