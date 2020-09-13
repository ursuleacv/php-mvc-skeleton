<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Actions\FindUserAction;
use Envms\FluentPDO\Exception;
use Envms\FluentPDO\Query;
use Laminas\Diactoros\Response\JsonResponse;
use PhpMvcCore\Application;
use PhpMvcCore\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;

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

    public function __construct(
        View $view,
        LoggerInterface $logger,
        FindUserAction $findUser,
        Query $query
    ) {
        $this->findUser = $findUser;
        $this->logger = $logger;
        $this->view = $view;
        $this->db = $query;
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
        $dbPath = explode(':', config('app.database.dsn'));
        $db = new \SQLite3($dbPath[1]);

        $query = '
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email varchar(50) NOT NULL UNIQUE,
                password varchar(50) NOT NULL,
                first_name TEXT NULL,
	            last_name TEXT NULL
            )';
        $db->exec($query) or die("Error Creating Table user_tokens");

        try {
            $password = \bin2hex(\random_bytes(10));

            $values = [
                'email' => \bin2hex(\random_bytes(5)) . '@example.com',
                'password' => \password_hash($password, PASSWORD_BCRYPT),
            ];

            $this->db->insertInto('users')->values($values)->execute();

            $users = $this->db->from('users')->orderBy('id DESC')->limit(10)->fetchAll();

        } catch (\Exception $e) {
            dd($e);
        }

//        foreach ($users as $user) {
//            echo "$user[id] $user[email] $user[password]\n";
//        }
//        dd($users);

        return new JsonResponse([
            'success' => true,
            'users' => $users,
        ]);
    }
}
