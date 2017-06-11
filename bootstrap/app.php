<?php

define('DIR', __DIR__);

use Respect\Validation\Validator as v;

session_start();

require DIR . '/../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDeatails' => true,
        'db' => [
            'driver' => 'mysql',
            'host' => 'mysql',
            'database' => 'slim',
            'username' => 'root',
            'password' => 'admin',
            'collation' => 'utf8_unciode_ci',
            'port' => '3306'
        ]
    ],
]);

$container = $app->getContainer();

//Eloquent
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['model'] = function ($container) use ($capsule) {
    return $capsule;
};

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

$container['auth'] = function ($container) {
    return new App\Auth\Auth;
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(DIR . '/../resources/views', [
        'cache' => false,
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->auth->check(),
        'user' => $container->auth->user(),
    ]);
    return $view;
};

$container['validator'] = function ($container) {
    return new App\Validation\Validator;
};

$container['AuthController'] = function ($container) { return new \App\Controllers\AuthController($container); };
$container['HomeController'] = function ($container) { return new \App\Controllers\HomeController($container); };
$container['UserController'] = function ($container) { return new \App\Controllers\UserController($container); };

/*$container['csrf'] = function ($container) {
  return new \Slim\Csrf\Guard;
};*/

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\FormDataMiddleware($container));
//$app->add(new \App\Middleware\CsrfViewMiddleware($container));

//$app->add($container->csrf);

v::with('App\\Validation\\Rules\\');

require DIR . '/../app/routes.php';

