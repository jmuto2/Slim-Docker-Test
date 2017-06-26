<?php

define('DIR', __DIR__);

use Respect\Validation\Validator as v;
use GuzzleHttp\Psr7\Request;

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

$models = [
    'user',
];
foreach ($models as $model) {
    $model_path = "\\App\\Models\\".$model;
    $container[$model.'Model'] = function ($container) use ($model_path) {
        return new $model_path;
    };
}

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

    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->auth->check(),
        'user' => $container->auth->user(),
    ]);
    return $view;
};

$container['apifier'] = function ($container) {
	return (object)['user_id' => 'GpYfW6pRCrGbnRDDx', 'token' => 'dbscKc6YvmXbos9d4GJXs2xRS'];
};

$container['client'] = function ($container) {
	return new GuzzleHttp\Client();
};

$res = new Request('GET', "https://api.apifier.com/v1/GpYfW6pRCrGbnRDDx/crawlers?token=dbscKc6YvmXbos9d4GJXs2xRS&offset=10&limit=99&desc=1");

var_dump($res);
die;
$controllers = [
    'Auth',
    'Home',
    'User',
		'Dashboard',
    'Helper',
];

foreach ($controllers as $controller) {
    $class = "\\App\\Controllers\\" . $controller . "Controller";
    $container[$controller."Controller"] = function ($container) use ($class) {
        return new $class($container);
    };
}

/*$container['csrf'] = function ($container) {
  return new \Slim\Csrf\Guard;
};*/
$container['auth'] = function ($container) {
    return new App\Auth\Auth;
};
$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\FormDataMiddleware($container));
//$app->add(new \App\Middleware\CsrfViewMiddleware($container));

//$app->add($container->csrf);

v::with('App\\Validation\\Rules\\');

require DIR . '/../app/routes.php';

