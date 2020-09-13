<?php
declare(strict_types=1);

namespace PhpMvcCore\ServiceProviders;

use Envms\FluentPDO\Query;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use PDO;

class DatabaseServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    protected $provides = [
        Query::class
    ];

    public function boot(): void
    {
        $container = $this->getContainer();

        $config = $container->get('Config');

        $fluent = new Query($this->createConnection($config->get('app.database')));

        $container->share(Query::class, $fluent);
    }

    public function register()
    {
        //
    }

    /**
     * @param array $config
     * @return PDO
     */
    protected function createConnection(array $config): PDO
    {
        list($username, $password) = [
            $config['username'] ?? null, $config['password'] ?? null,
        ];

        return new PDO($config['dsn'], $username, $password, $config['options']);
    }
}
