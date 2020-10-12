<?php

declare(strict_types=1);

use App\Controllers\Api\UserController;
use App\Controllers\ExampleController;
use League\Route\RouteGroup;

$router = new League\Route\Router();

$router->get('/', [ExampleController::class, 'index']);
$router->get('/profile', [ExampleController::class, 'profile']);
$router->get('/session', [ExampleController::class, 'session']);
$router->get('/database', [ExampleController::class, 'database']);
$router->get('/create', [ExampleController::class, 'create']);
$router->post('/create', [ExampleController::class, 'store']);
$router->get('/auth/login', [ExampleController::class, 'login']);
$router->get('/auth/callback', [ExampleController::class, 'callback']);

$router->group('api', function (RouteGroup $route) {
    $route->get('/users', [UserController::class, 'index']);
    $route->post('/users', [UserController::class, 'create']);
    $route->get('/users/{id}', [UserController::class, 'show']);
    $route->put('/users/{id}', [UserController::class, 'update']);
    $route->delete('/users/{id}', [UserController::class, 'delete']);
});

return $router;
