<?php

namespace ROrier\Container\Components\Bootstraps;

use Exception;
use ROrier\Config\Interfaces\ParametersInterface;
use ROrier\Container\Interfaces\ServiceLibraryInterface;
use ROrier\Container\Services\Container;
use ROrier\Config\Services\Analyzer;
use ROrier\Container\Services\DelayedProxies\ContainerProxy;
use ROrier\Config\Services\ConfigParsers\ArrayParameterParser;
use ROrier\Config\Services\ConfigParsers\ConstantParser;
use ROrier\Config\Services\ConfigParsers\EnvParser;
use ROrier\Container\Services\ConfigParsers\ServiceParser;
use ROrier\Config\Services\ConfigParsers\StringParameterParser;
use ROrier\Container\Services\ServiceBuilderModules\CallsModule;
use ROrier\Container\Services\ServiceBuilderModules\CatchModule;
use ROrier\Container\Services\ServiceBuilderModules\ConfigModule;
use ROrier\Container\Services\ServiceBuilderModules\ConstructorModule;
use ROrier\Container\Services\ServiceBuilderModules\FactoryModule;
use ROrier\Container\Services\Factories\ServiceFactory;
use ROrier\Container\Services\Builders\ServiceWorkbenchBuilder;
use ROrier\Container\Services\Builders\ServiceBuilder;

class ContainerBootstrap
{
    private ParametersInterface $parameters;

    private ServiceLibraryInterface $library;

    private ?Container $container = null;

    /**
     * ContainerBootstrap constructor.
     * @param ParametersInterface $parameters
     * @param ServiceLibraryInterface $library
     */
    public function __construct(ParametersInterface $parameters, ServiceLibraryInterface $library)
    {
        $this->parameters = $parameters;
        $this->library = $library;
    }

    /**
     * @return self
     * @throws Exception
     */
    public function build(): self
    {
        $delayedContainer = new ContainerProxy();

        $argumentAnalyzer = new Analyzer([
            new ConstantParser(),
            new EnvParser(),
            new StringParameterParser($this->parameters),
            new ArrayParameterParser($this->parameters),
            new ServiceParser($delayedContainer)
        ]);

        if (method_exists($this->parameters, 'getAnalyzer')) {
            $configAnalyzer = $this->parameters->getAnalyzer();
        } else {
            $configAnalyzer = new Analyzer([
                new ConstantParser(),
                new EnvParser(),
                new StringParameterParser($this->parameters),
                new ArrayParameterParser($this->parameters)
            ]);
        }

        $serviceBuilder = new ServiceBuilder([
            new ConstructorModule($argumentAnalyzer),
            new FactoryModule($delayedContainer, $argumentAnalyzer)
        ],[
            new ConfigModule($configAnalyzer),
            new CallsModule(),
            new CatchModule($this->library)
        ]);

        $serviceWorkbenchBuilder = new ServiceWorkbenchBuilder(
            $delayedContainer,
            $argumentAnalyzer,
            $serviceBuilder
        );

        $serviceFactory = new ServiceFactory(
            $delayedContainer,
            $this->library,
            $serviceWorkbenchBuilder
        );

        $this->container = new Container($this->library, $serviceFactory);

        $delayedContainer->setContainer($this->container);

        $this->container->setServices([
            'container' => $this->container,
            'parameters' => $this->parameters,

            'library.services' => $this->library,

            'factory.services' => $serviceFactory,
            'builder.workbench.services' => $serviceWorkbenchBuilder,
            'builder.service' => $serviceBuilder,

            'analyzer.config' => $configAnalyzer,
            'analyzer.argument' => $argumentAnalyzer
        ]);

        if (method_exists($this->library, 'getCompilator') && ($this->library->getCompilator() !== null)) {
            $this->container->setService('compilator.spec.services', $this->library->getCompilator());
        }

        return $this;
    }

    /**
     * @return Container
     * @throws Exception
     */
    public function get(): Container
    {
        if ($this->container === null) {
            throw new Exception("Service container has not yet been built.");
        }

        return $this->container;
    }
}
