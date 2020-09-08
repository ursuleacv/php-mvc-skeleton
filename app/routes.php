<?php
declare(strict_types=1);

use App\Controllers\Api\UserController;
use App\Controllers\HomeController;
use League\Route\RouteGroup;

$router = new League\Route\Router;

$router->get('/', [HomeController::class, 'index']);
$router->get('/profile', [HomeController::class, 'profile']);

$router->group('api', function (RouteGroup $route) {
    $route->get('/users', [UserController::class, 'index']);
    $route->post('/users', [UserController::class, 'create']);
    $route->get('/users/{id}', [UserController::class, 'show']);
    $route->put('/users/{id}', [UserController::class, 'update']);
    $route->delete('/users/{id}', [UserController::class, 'delete']);
});

return $router;
