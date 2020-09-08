<?php
declare(strict_types=1);

namespace PhpMvcCore\ServiceProviders;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;

class RouterServiceProvider extends AbstractServiceProvider
{
    protected $provides = [Router::class];

    /**
     * @return void
     */
    public function register(): void
    {
        $strategy = new ApplicationStrategy();
        $strategy->setContainer($this->getContainer());

        $router = require $this->getContainer()->get('baseDir') . '/app/routes.php';
        $router->setStrategy($strategy);

        $this->getContainer()->share(Router::class, $router);
    }
}
