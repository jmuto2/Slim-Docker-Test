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

    /*try
    {
        $pdo =  new PDO("mysql:host='192.168.99.100';port='8181';dbname='slim';", 'root', 'admin');
        var_dump($pdo);
        die;
    }
    catch (PDOException $e)
    {
        echo $e->getMessage();

    }*/
};
$pdo =   new \PDO('mysql:host=mysql;port=3306;dbname=slim', 'root', 'admin');
$sth = $pdo->prepare("SELECT * FROM users");
$sth->execute();

/* Fetch all of the remaining rows in the result set */
$result = $sth->fetchAll();

var_dump($result);
die;
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

