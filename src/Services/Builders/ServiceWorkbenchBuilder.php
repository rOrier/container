<?php

namespace ROrier\Services\Services\Builders;

use ROrier\Config\Interfaces\AnalyzerInterface;
use ROrier\Services\Components\ServiceCallDelayer;
use ROrier\Services\Components\ServiceWorkbench;
use ROrier\Services\Interfaces\ContainerInterface;
use ROrier\Services\Interfaces\ServiceWorkbenchBuilderInterface;
use ROrier\Services\Interfaces\ServiceWorkbenchInterface;
use ROrier\Services\Interfaces\ServiceBuilderInterface;

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
