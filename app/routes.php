<?php

$app->get('/home', 'HomeController:index');

$app->get('/sign_up', 'AuthController:getSignUp')->setName('auth.signup');
$app->post('/sign_up', 'AuthController:postSignUp');

$app->get('/add', 'UserController:add');
