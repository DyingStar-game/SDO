<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/sdo');
$dbConfig = include(__DIR__ . '/../phinx.php');

$capsule = new Capsule();
$myDatabase = $dbConfig['environments'][$dbConfig['environments']['default_environment']];
$configdb = [
  'driver'    => $myDatabase['adapter'],
  'host'      => $myDatabase['host'],
  'database'  => $myDatabase['name'],
  'username'  => $myDatabase['user'],
  'password'  => $myDatabase['pass'],
  'charset'   => $myDatabase['charset'],
  // 'collation' => $myDatabase['collation'],
];
$capsule->addConnection($configdb);
$capsule->setEventDispatcher(new Dispatcher(new Container()));
$capsule->setAsGlobal();
$capsule->bootEloquent();


// Servers
$app->post('/servers/register', \App\Controllers\Server::class . ':postRegister');
$app->post('/servers/{id:[a-z0-9]+}/heavy', \App\Controllers\Server::class . ':postTooHeavy');
$app->post('/servers/{id:[a-z0-9]+}/free', \App\Controllers\Server::class . ':postFree');
$app->post('/servers/{id:[a-z0-9]+}/players', \App\Controllers\Server::class . ':postPlayers');
$app->get('/servers', \App\Controllers\Server::class . ':getAll');
$app->get('/servers/onlyactive', \App\Controllers\Server::class . ':getAllActiveOnly');
$app->get('/servers/{id:[a-z0-9]+}', \App\Controllers\Server::class . ':getItem');

// Players
$app->get('/players', \App\Controllers\Player::class . ':getAll');

$app->run();
