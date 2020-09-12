<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Actions\FindUserAction;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use PhpMvcCore\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class HomeController
{

    /**
     * @var FindUserAction
     */
    private FindUserAction $findUser;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(FindUserAction $findUser, LoggerInterface $logger)
    {
        $this->findUser = $findUser;
        $this->logger = $logger;
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
        $user = $this->findUser->byEmail('john@gmail.com');

        $this->logger->info('test message', ['user' => $user]);

        \logger()->info('This is logged from a global function');

        $debug = Application::$app->getContainer()->get('config')->get('app.debug');
        $env = Application::$app->getContainer()->get('config')->get('app.env');
        $timezone = Application::$app->getContainer()->get('config')->get('app.timezone');

        $debugFunc = config('app.debug');

//        return new HtmlResponse('<p>Hello</p>');
        return new JsonResponse([
            'debug' => $debug,
            'debug_' => $debugFunc,
            'env' => $env,
            'timezone' => $timezone,
            'user' => $user,
        ]);
    }

    public function profile(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(['data' => 'user']);
    }
}
