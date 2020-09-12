<?php
declare(strict_types=1);

namespace PhpMvcCore\ServiceProviders;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Plates\Engine;
use League\Plates\Template;
use PhpMvcCore\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TemplateServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        Engine::class,
        Template::class,
        View::class,
    ];

    public function boot()
    {
        //
    }

    public function register()
    {
        $container = $this->getContainer();

        $basePath = $container->get('baseDir');

        $platesEngine = Engine::createWithConfig([
            'base_dir' => $basePath . '/app/Views',
            'escape_encoding' => 'UTF-8',
            'ext' => 'php',
        ]);

        $response = $container->get(ResponseInterface::class);

        $container->share(Engine::class, $platesEngine);
        $container->share(View::class, new View($response, $platesEngine));
    }
}
