<?php
declare(strict_types=1);

namespace PhpMvcCore\ServiceProviders;

use League\Container\ServiceProvider\BootableServiceProviderInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use PhpMvcCore\Config;

class ConfigServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    protected $provides = [
        'Config',
    ];

    protected $aliases = [
        Config::class => 'Config',
    ];

    public function boot()
    {
        //
    }

    public function register()
    {
        $container = $this->getContainer();

        $basePath = $container->get('baseDir');

        $config = new Config($basePath);
        $conf = $config->loadConfig();

        $container->share('Config', $conf);
    }
}
