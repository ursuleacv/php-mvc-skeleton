<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Actions\FindUserAction;
use Auth0\SDK\Auth0;
use Auth0\SDK\Exception\ApiException;
use Auth0\SDK\Exception\CoreException;
use Auth0\SDK\Store\EmptyStore;
use Auth0\SDK\Store\SessionStore;
use Auth0\SDK\Store\StoreInterface;
use Envms\FluentPDO\Query;
use Laminas\Diactoros\Response\JsonResponse;
use PhpMvcCore\Application;
use PhpMvcCore\View;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use PSR7Sessions\Storageless\Session\LazySession;
use PSR7Sessions\Storageless\Session\SessionInterface;
use Sirius\Validation\Validator;

class ExampleController
{
    /**
     * @var FindUserAction
     */
    private FindUserAction $findUser;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var View
     */
    private View $view;

    private Query $db;
    /**
     * @var Validator
     */
    private Validator $validator;

    public function __construct(
        View $view,
        LoggerInterface $logger,
        FindUserAction $findUser,
        Query $query,
        Validator $validator
    ) {
        $this->findUser = $findUser;
        $this->logger = $logger;
        $this->view = $view;
        $this->db = $query;
        $this->validator = $validator;
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
        // or
        \logger()->info('This is logged from a global function');

        $debug = Application::$app->getContainer()->get('Config')->get('app.debug');
        $env = Application::$app->getContainer()->get('Config')->get('app.env');
        $timezone = Application::$app->getContainer()->get('Config')->get('app.timezone');

        // or
        $debug = config('app.debug');

//        return new HtmlResponse('<p>Hello</p>');
        return new JsonResponse([
            'debug' => $debug,
            'env' => $env,
            'timezone' => $timezone,
            'user' => $user,
        ]);
    }

    public function session(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var \PSR7Sessions\Storageless\Session\SessionInterface $session
         */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $session->set('counter', $session->get('counter', 0) + 1);
        return new JsonResponse([
            'counter' => $session->get('counter')
        ]);
    }

    public function profile(ServerRequestInterface $request): ResponseInterface
    {
        $name = $request->getQueryParams()['name'] ?? 'John';

        return $this->view->render('profile', [
            'name' => $name,
        ]);
        // or
        return \view('profile', [
            'name' => $name,
        ]);
    }

    public function database(ServerRequestInterface $request): ResponseInterface
    {
//        $query = '
//            CREATE TABLE IF NOT EXISTS users (
//                id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//                email varchar(50) NOT NULL UNIQUE,
//                password varchar(255) NOT NULL,
//                first_name varchar(50) NULL,
//              last_name varchar(50) NULL,
//              created_at int(11) UNSIGNED NULL
//            )';
//
//        try {
//            $this->db->getPdo()->exec($query);
//        } catch (\PDOException $e) {
//            dd($e);
//        }

        try {
            $password = \bin2hex(\random_bytes(10));

            $values = [
                'email' => \bin2hex(\random_bytes(5)) . '@example.com',
                'password' => \password_hash($password, PASSWORD_BCRYPT),
                'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ];

            $this->db->insertInto('users')->values($values)->execute();

            $users = $this->db->from('users')->orderBy('id DESC')->limit(10)->fetchAll();

            $user = $this->db->from('users')->where('id', 1)->fetch();
        } catch (\Exception $e) {
            dd($e);
        }

//        foreach ($users as $user) {
//            echo "$user[id] $user[email] $user[password]\n";
//        }
//        dd($users);

        return new JsonResponse([
            'success' => true,
            'user' => $user,
            'users' => $users,
        ]);
    }

    public function create(): ResponseInterface
    {
        return $this->view->render('create', [
            'result' => false,
            'errors' => [],
            'data' => [],
        ]);
    }

    public function store(ServerRequestInterface $request): ResponseInterface
    {
        $errors = [];
        $result = false;
        $data = $request->getParsedBody();
        $this->validator->add([
            'first_name:First Name' => 'required | alpha | minlength(3) | maxlength(50)',
            'last_name:Last Name' => 'required | alpha | minlength(3) | maxlength(50)',
            'email:Email' => 'required | email | unique(users,email)',
            // 'email:Email' => 'required | email | exists(users,email)',
            'password:Password' => 'required | minlength(8) | maxlength(24)'
        ]);
        if ($this->validator->validate($data)) {
            try {
                $result = $this->db->insertInto('users')->values([
                    'first_name' => $data['first_name'] ?? null,
                    'last_name'  => $data['last_name'] ?? null,
                    'email'      => $data['email'],
                    'password'   => password_hash($data['password'], PASSWORD_DEFAULT)
                ])->execute();
                $data = [];
            } catch (\Exception $e) {
                $this->validator->addMessage('email', $e->getMessage());
                $errors = $this->validator->getMessages();
            }
        } else {
            $errors = $this->validator->getMessages();
        }

        return $this->view->render('create', compact('result', 'errors', 'data'));
    }

    public function login(RequestInterface $request): ResponseInterface
    {
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        $auth0 = new Auth0([
            'domain' => env('AUTH0_DOMAIN'),
            'client_id' => env('AUTH0_CLIENT_ID'),
            'client_secret' => env('AUTH0_CLIENT_SECRET'),
            'redirect_uri' => 'http://localhost:8000/auth/callback',
            'scope' => 'openid email profile',
            'store' => new EmptyStore(),
        ]);

//        When using native session
//        try {
//            $userInfo = $auth0->getUser();
//        } catch (\Exception $e) {
//            dd($e);
//        }
//
//        if (empty($userInfo)) {
//            $auth0->login();
//        }
//
//        dd($userInfo);

        $user = $session->get('user');

        if (empty($user)) {
            $auth0->login();
        }

        dd($user);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws CoreException
     */
    public function callback(RequestInterface $request): ResponseInterface
    {
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        $auth0 = new Auth0([
            'domain' => env('AUTH0_DOMAIN'),
            'client_id' => env('AUTH0_CLIENT_ID'),
            'client_secret' => env('AUTH0_CLIENT_SECRET'),
            'redirect_uri' => '/auth/callback',
            'scope' => 'openid email profile',
            'store' => new EmptyStore(),// Doesn't work well with storage-less session
        ]);

        try {
            $userInfo = $auth0->getUser();
            if ($userInfo) {
                $session->set('user', [
                    'id' => $userInfo['sub'],
                    'email' => $userInfo['email'],
                ]);
            }
        } catch (\Exception $e) {
//            dd($e);
        }

        $user = $session->get('user');

        if (empty($user)) {
            $auth0->login();
        }

        return new JsonResponse([
            'user' => $user,
        ]);
    }

    private function getStorage(ServerRequestInterface $request)
    {
        return new class($request) implements StoreInterface {
            private $session;
            public function __construct(ServerRequestInterface $request)
            {
                /** @var SessionInterface $session */
                $this->session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
            }

            public function set(string $key, $value)
            {
                $this->session->set($key, $value);
            }
            public function get(string $key, $default = null)
            {
                return $this->session->get($key, $default);
            }
            public function delete(string $key)
            {
                $this->session->remove($key);
            }

        };
    }
}
