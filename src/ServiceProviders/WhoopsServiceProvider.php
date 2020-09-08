<?php

namespace PhpMvcCore\ServiceProviders;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class WhoopsServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    protected $provides = [
        PrettyPageHandler::class,
        Run::class,
    ];

    public function boot()
    {
        (new Run)->pushHandler(new PrettyPageHandler)->register();
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // TODO: Implement register() method.
    }
}
