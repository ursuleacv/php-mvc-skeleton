<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserController
{
    const DEMO_USER = [
        'email' => 'john@gmail.com',
        'name' => 'John Doe',
    ];

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
        return new JsonResponse(['data' => [self::DEMO_USER]]);
    }

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $user = array_merge(['id' => $id], self::DEMO_USER);
        return new JsonResponse(['data' => $user]);
    }

    public function create(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(['data' => self::DEMO_USER]);
    }

    public function update(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(['data' => self::DEMO_USER]);
    }

    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse([]);
    }
}
