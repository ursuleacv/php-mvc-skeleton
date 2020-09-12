<?php
declare(strict_types=1);

use PhpMvcCore\Application;
use Psr\Log\LoggerInterface;

if (!function_exists('container')) {
    /**
     * @param string|null $make
     * @return League\Container\Container|LoggerInterface|object
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
        return  container('config')->get($key);
    }
}

if (!function_exists('env')) {
    /**
     * @param string $var environment variable name.
     * @return mixed
     */
    function env(string $var)
    {
        return isset($_ENV[$var]) ? $_ENV[$var] : false;
    }
}