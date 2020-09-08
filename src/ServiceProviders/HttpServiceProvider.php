<?php
declare(strict_types=1);

namespace PhpMvcCore\ServiceProviders;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Container\ServiceProvider\AbstractServiceProvider;
use PhpMvcCore\Application;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpServiceProvider extends AbstractServiceProvider
{
    /**
     * The provided array is a way to let the container
     * know that a service is provided by this service
     * provider. Every service that is registered via
     * this service provider must have an alias added
     * to this array or it will be ignored.
     *
     * @var array
     */
    protected $provides = [
        ServerRequestInterface::class,
        ResponseInterface::class,
        EmitterInterface::class,
    ];


    /**
     * @return void
     */
    public function boot()
    {
//        $this->getContainer()->inflector(AbstractController::class)
//            ->invokeMethod('setTwig', ['twig'])
//            ->invokeMethod('setSettings', [SettingRepositoryInterface::class]);
//
//        $this->getContainer()->inflector(AbstractWidgetController::class)
//            ->invokeMethod('setWidgetExtension', ['twigWidgetExtension']);
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->getContainer()->share(ServerRequestInterface::class, function () {
            return ServerRequestFactory::fromGlobals();
        });

        $this->getContainer()->share(ResponseInterface::class, Response::class);

        $this->getContainer()->share(EmitterInterface::class, SapiEmitter::class);
    }
}
