<?php
declare(strict_types=1);

namespace App\Controllers;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use PhpMvcCore\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController
{
    public function __construct()
    {
        //
    }

    /**
     * Controller.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $debug = Application::$app->getContainer()->get('config')->get('app.debug');
        $env = Application::$app->getContainer()->get('config')->get('app.env');
        $timezone = Application::$app->getContainer()->get('config')->get('app.timezone');

//        return new HtmlResponse('<p>Hello</p>');
        return new JsonResponse([
            'debug' => $debug,
            'env' => $env,
            'timezone' => $timezone,
        ]);
    }

    public function profile(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(['data' => 'user']);
    }
}
