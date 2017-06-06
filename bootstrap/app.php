<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';


$app = new \Slim\App([
    'settings' => [
        'displayErrorDeatails' => true,

    ]
]);

$app->get('/', function ($request, $response) {
    return 'Home';
});