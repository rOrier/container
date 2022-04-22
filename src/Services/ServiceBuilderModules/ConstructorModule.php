<?php

namespace ROrier\Services\Services\ServiceBuilderModules;

use ReflectionClass;
use ReflectionException;
use ROrier\Services\Components\ServiceSpec;
use ROrier\Config\Interfaces\AnalyzerInterface;
use ROrier\Services\Interfaces\ServiceBuilderModuleInterface;
use ROrier\Services\Components\ServiceDraft;
use ROrier\Services\Exceptions\ContainerException;
use ROrier\Config\Foundations\AbstractParsingException;

class ConstructorModule implements ServiceBuilderModuleInterface
{
    private AnalyzerInterface $analyzer;

    /**
     * ConstructorModule constructor.
     * @param AnalyzerInterface $analyzer
     */
    public function __construct(AnalyzerInterface $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * @inheritDoc
     */
    public function manages(ServiceSpec $spec): bool
    {
        return (bool) $spec['class'];
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     * @throws ContainerException
     * @throws AbstractParsingException
     */
    public function process(ServiceDraft $draft): void
    {
        $spec = $draft->getSpec();

        $reflexionClass = new ReflectionClass($spec['class']);

        $arguments = $spec->getArguments();
        $arguments = $this->analyzer->parse($arguments);

        $service = $reflexionClass->newInstanceArgs($arguments);

        $draft->setService($service);
    }
}
