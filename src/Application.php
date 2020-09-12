<?php
declare(strict_types=1);

namespace PhpMvcCore;

use Exception;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use League\Container\Container;
use League\Container\ReflectionContainer;
use League\Route\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;

final class Application
{
    private string $baseDir;

    protected Container $container;

    public static Application $app;

    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->baseDir = dirname(__DIR__);

        $this->container = new Container;
        $this->container->delegate(new ReflectionContainer);
        $this->container->add('baseDir', $this->baseDir);

        self::$app = $this;

        $this->loadServiceProviders();
    }

    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        /** @var Router $router */
        $router = $this->container->get(Router::class);
        /** @var ServerRequestInterface $request */
        $request = $this->container->get(ServerRequestInterface::class);
        /** @var ResponseInterface $response */
        $response = $this->container->get(ResponseInterface::class);
        /** @var EmitterInterface $emitter */
        $emitter = $this->container->get(EmitterInterface::class);

        $router->middleware($this->container->get(SessionMiddleware::class));
        $response = $router->dispatch($request);

        $emitter->emit($response);
    }

    private function loadServiceProviders()
    {
        // TODO: load Whoops only in debug mode
        $providers = [
            new \PhpMvcCore\ServiceProviders\ConfigServiceProvider,
            new \PhpMvcCore\ServiceProviders\LoggerServiceProvider,
            new \PhpMvcCore\ServiceProviders\HttpServiceProvider,
            new \PhpMvcCore\ServiceProviders\SessionServiceProvider,
            new \PhpMvcCore\ServiceProviders\RouterServiceProvider,
            new \PhpMvcCore\ServiceProviders\TemplateServiceProvider,
            new \PhpMvcCore\ServiceProviders\WhoopsServiceProvider,
        ];
        foreach ($providers as $provider) {
            $this->container->addServiceProvider($provider);
        }
    }
}
