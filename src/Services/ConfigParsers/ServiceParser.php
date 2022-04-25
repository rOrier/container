<?php

namespace ROrier\Container\Services\ConfigParsers;

use ROrier\Config\Interfaces\ParserInterface;
use ROrier\Container\Interfaces\ContainerInterface;

class ServiceParser implements ParserInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function match(string $var): bool
    {
        return (substr($var, 0, 1) === '@');
    }

    /**
     * @inheritDoc
     */
    public function process(string $var)
    {
        $serviceName = substr($var, 1);
        $var = $this->container->get($serviceName);

        return $var;
    }
}