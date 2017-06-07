<?php

define('DIR', __DIR__);

session_start();

require DIR . '/../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDeatails' => true,

    ]
]);


$container = $app->getContainer();

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(DIR . '/../resources/views', [
        'cache' => false,
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    return $view;
};

$container['HomeController'] = function ($container) {
    return new \App\Controllers\HomeController($container);
};

require DIR . '/../app/routes.php';