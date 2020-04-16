<?php

namespace Framework\Command;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;


/**
 * Created by PhpStorm.
 * User: rogozhuk
 * Date: 13.04.20
 * Time: 9:27
 */

class KernelCommand implements Command
{
    protected $containerBuilder;
    protected $routeCollection;
    protected $urlConfig;

    public function __construct($containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
        $this->urlConfig = __DIR__ . DIRECTORY_SEPARATOR . '..' .  DIRECTORY_SEPARATOR .  '..' .  DIRECTORY_SEPARATOR . 'config';
    }

    public function registerConfigs(): void
    {
        try {
            $fileLocator = new FileLocator($this->urlConfig);
            $loader = new PhpFileLoader($this->containerBuilder, $fileLocator);
            $loader->load('parameters.php');
        } catch (\Throwable $e) {
            die('Cannot read the config file. File: ' . __FILE__ . '. Line: ' . __LINE__);
        }
    }

    public function registerRoutes(): void
    {
        $this->routeCollection = require $this->urlConfig . DIRECTORY_SEPARATOR . 'routing.php';
        $this->containerBuilder->set('route_collection', $this->routeCollection);
    }
}