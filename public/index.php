<?php
declare(strict_types=1);

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Container\Container;
use League\Container\ReflectionContainer;
use PhpMvcCore\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../vendor/autoload.php';
error_reporting(E_ALL);

$dotEnv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotEnv->load();
$dotEnv->required(['APP_DEBUG'])->isBoolean();
$dotEnv->required(['APP_ENV'])->notEmpty();
$dotEnv->required(['APP_KEY'])->notEmpty();

try {
    $app = new \PhpMvcCore\Application();
    $app->run();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}