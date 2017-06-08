<?php

$app->get('/', 'HomeController:index');
$app->get('/add', 'UserController:add');