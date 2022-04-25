<?php

namespace ROrier\Container\Services\Builders;

use ROrier\Config\Interfaces\AnalyzerInterface;
use ROrier\Container\Components\ServiceCallDelayer;
use ROrier\Container\Components\ServiceWorkbench;
use ROrier\Container\Interfaces\ContainerInterface;
use ROrier\Container\Interfaces\ServiceWorkbenchBuilderInterface;
use ROrier\Container\Interfaces\ServiceWorkbenchInterface;
use ROrier\Container\Interfaces\ServiceBuilderInterface;

class ServiceWorkbenchBuilder implements ServiceWorkbenchBuilderInterface
{
    private ContainerInterface $container;

    private AnalyzerInterface $argumentAnalyzer;

    private ServiceBuilderInterface $serviceBuilder;

    /**
     * ServiceWorkbenchBuilder constructor.
     * @param ContainerInterface $container
     * @param AnalyzerInterface $argumentAnalyzer
     * @param ServiceBuilderInterface $serviceBuilder
     */
    public function __construct(
        ContainerInterface $container,
        AnalyzerInterface $argumentAnalyzer,
        ServiceBuilderInterface $serviceBuilder
    ) {
        $this->container = $container;
        $this->argumentAnalyzer = $argumentAnalyzer;
        $this->serviceBuilder = $serviceBuilder;
    }

    /**
     * @return ServiceWorkbenchInterface
     */
    public function build(): ServiceWorkbenchInterface
    {
        $callDelayer = new ServiceCallDelayer($this->container, $this->argumentAnalyzer);

        return new ServiceWorkbench(
            $this->serviceBuilder,
            $callDelayer
        );
    }
}
