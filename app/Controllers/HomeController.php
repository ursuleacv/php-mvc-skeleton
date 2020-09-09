<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Actions\FindUserAction;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use PhpMvcCore\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController
{
    /**
     * @var FindUserAction
     */
    private FindUserAction $findUser;

    public function __construct(FindUserAction $findUser)
    {
        $this->findUser = $findUser;
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
//        dd(Application::$app->getContainer()->get('config')->get('app'));
        $debug = Application::$app->getContainer()->get('config')->get('app.debug');
        $env = Application::$app->getContainer()->get('config')->get('app.env');
        $timezone = Application::$app->getContainer()->get('config')->get('app.timezone');

//        return new HtmlResponse('<p>Hello</p>');
        return new JsonResponse([
            'debug' => $debug,
            'env' => $env,
            'timezone' => $timezone,
            'user' => $this->findUser->byEmail('john'),
        ]);
    }

    public function profile(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(['data' => 'user']);
    }
}
