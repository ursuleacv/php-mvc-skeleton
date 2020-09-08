<?php

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Container\Container;
use League\Container\ReflectionContainer;
use PhpMvcCore\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/vendor/autoload.php';
error_reporting(E_ALL);

define('ROOT_PATH', dirname(__DIR__));

$app = new \PhpMvcCore\Application();
$app->run();




//$router = new League\Route\Router;
//
//$router->get('/', [\PhpMvcCore\HomeController::class, 'index']);
//$router->get('/user', [\PhpMvcCore\HomeController::class, 'user']);
//
//try {
//    $container = new Container;
//    $container->delegate(new ReflectionContainer);
//
//    $container->share(ServerRequestInterface::class, function () {
//        return ServerRequestFactory::fromGlobals();
//    });
//
//    $container->share(ResponseInterface::class, Response::class);
//    $container->share(EmitterInterface::class, SapiEmitter::class);
//
//    $request = $container->get(ServerRequestInterface::class);
//
//    $response = $router->dispatch($request);
//
//} catch (\League\Route\Http\Exception\NotFoundException $e) {
//    var_dump($e->getMessage()); die;
//}
//$container->get(EmitterInterface::class)->emit($response);
