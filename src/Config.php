<?php
declare(strict_types=1);

namespace PhpMvcCore;

use Dotenv\Dotenv;

class Config
{
    private string $rootPath;

    // TODO: replace with https://github.com/hassankhan/config
    public function __construct($rootPath)
    {
        $this->rootPath = $rootPath;
    }

    public function loadConfig(): ConfigRepository
    {
        $config = $this->loadConfigurationFiles();

        date_default_timezone_set($config->get('app.timezone', 'UTC'));

        mb_internal_encoding('UTF-8');

        return $config;
    }

    /**
     * @return ConfigRepository
     */
    protected function loadConfigurationFiles(): ConfigRepository
    {
        $files = $this->getConfigurationFiles();

        if (! isset($files['app'])) {
            throw new \RuntimeException('Unable to load the "app" configuration file.');
        }

        $configRepo = new ConfigRepository();

        foreach ($files as $key => $path) {
            $configRepo->set($key, require $path);
        }

        return $configRepo;
    }

    protected function getConfigurationFiles()
    {
        $files = [];

        $configPath = realpath($this->configPath());

        if (!$configPath) {
            throw new \RuntimeException('Unable to load the "app" configuration file. "app/Config" folder not found');
        }

        $iterator = new \FilesystemIterator($configPath, \FilesystemIterator::KEY_AS_PATHNAME);

        foreach ($iterator as $key => $file) {
            if (preg_match('/[a-zA-Z0-9_]+\\.php/i', $file->getFilename())) {
                $files[basename($file->getRealPath(), '.php')] =  $file->getRealPath();
            }
        }

        return $files;
    }

    private function configPath()
    {
        return $this->rootPath . DIRECTORY_SEPARATOR.'app' . DIRECTORY_SEPARATOR . 'Config';
    }
}
