<?php

namespace Framework\Command;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Created by PhpStorm.
 * User: rogozhuk
 * Date: 13.04.20
 * Time: 9:26
 */

interface Command
{
    public function __construct(ContainerBuilder $containerBuilder);

    public function registerConfigs(): void;

    public function registerRoutes(): void;
}