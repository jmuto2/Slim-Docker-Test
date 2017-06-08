<?php

define('DIR', __DIR__);

session_start();

require DIR . '/../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDeatails' => true,
    ],
]);

$container = $app->getContainer();

$container['config'] = function ($container) {
    $config = file_get_contents(__DIR__ . '/config.json');
    $config = json_decode($config);

    return $config;
};

$container['db'] = function ($container) {
    $db = $container->config->db;

    try
    {
        return  new \PDO("mysql:host=$db->host;port=$db->port;dbname=$db->database", $db->username, $db->password);
    }
    catch (PDOException $e)
    {
        return $e->getMessage();
    }
};

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

$controllers = [
    'Home',
    'Users',
];

foreach ($controllers as $controller) {
    $class = "\\App\\Controllers\\" . $controller;
    $container[$controller] = function ($container) use ($class) {
        return new $class($container);
    };
}

require DIR . '/../app/routes.php';

