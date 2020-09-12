<?php
declare(strict_types=1);

use League\Container\Container;
use PhpMvcCore\Application;
use PhpMvcCore\View;
use Psr\Log\LoggerInterface;

if (!function_exists('container')) {
    /**
     * @param string|null $make
     * @return Container|LoggerInterface|View|object
     */
    function container(string $make = null)
    {
        $c = Application::$app->getContainer();
        return $make ? $c->get($make) : $c;
    }
}

if (!function_exists('logger')) {
    /**
     * @return LoggerInterface
     */
    function logger()
    {
        return container(LoggerInterface::class);
    }
}

if (!function_exists('app')) {
    /**
     * @return Application
     */
    function app()
    {
        return Application::$app;
    }
}

if (!function_exists('config')) {
    /**
     * @param string $key
     * @param null $default
     * @return string|null|array|mixed
     */
    function config(string $key, $default = null)
    {
        return  container('Config')->get($key);
    }
}

if (!function_exists('env')) {
    /**
     * @param string $var environment variable name.
     * @param null $default
     * @return mixed
     */
    function env(string $var, $default = null)
    {
        return isset($_ENV[$var]) ? $_ENV[$var] : $default;
    }
}

if (!function_exists('view')) {
    /**
     * @param string $template
     * @param array $params
     * @param int $status
     * @return mixed
     */
    function view(string $template, array $params = [], int $status = 200)
    {
        return container(View::class)
            ->render($template, $params, $status);
    }
}
