<?php

$routes = (object) [
    'auth' => [
        ['get', '/home',
            'AuthController:getSignUp', 'auth.getsignup'],
        ['post', '/home',
            'AuthController:postSignUp', 'auth.signup'],
        ['get', '/signin',
            'AuthController:getSignIn', 'auth.getsignin'],
        ['post', '/signin',
            'AuthController:postSignIn', 'auth.signin'],
        ['get', '/signout',
            'AuthController:getSignOut', 'auth.signout'],
    ],
    'user' => [
        ['get', '/dashboard',
            'HomeController:index', 'home'],
    ],
    'photo' => [
        ['post', '/photo',
            'PhotoController:update', 'user.photoupdate'],
    ]
];

foreach ($routes as $route_type) {
    foreach ($route_type as $route) {
        $app->{$route[0]}($route[1], $route[2])->setName($route[3]);
    }
}

$out = [];
foreach ($routes as $route_type) {
    foreach ($route_type as $route) {
        $out[$route[3]] = $route[1];
    }
}

$container->view->getEnvironment()->addGlobal('routes', $out);



