<?php
declare(strict_types=1);

namespace PhpMvcCore\ServiceProviders;

use Katzgrau\KLogger\Logger;
use League\Container\ServiceProvider\AbstractServiceProvider;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class LoggerServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        LoggerInterface::class,
        Logger::class,
    ];

    protected $aliases = [
        Logger::class => 'logger',
    ];

    public function boot()
    {
        //
    }

    public function register()
    {
        $container = $this->getContainer();

        $logsPath = $container->get('baseDir') . '/app/storage/logs';

        $logger = new Logger($logsPath, LogLevel::DEBUG, ['extension' => 'log']);

        $container->share(LoggerInterface::class, $logger);
    }
}
