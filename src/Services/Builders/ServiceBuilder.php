<?php

namespace ROrier\Services\Services\Builders;

use ROrier\Services\Components\ServiceDraft;
use ROrier\Services\Components\ServiceSpec;
use ROrier\Config\Exceptions\ConfigurationException;
use ROrier\Services\Interfaces\ServiceBuilderModuleInterface;
use ROrier\Services\Interfaces\ServiceBuilderInterface;

class ServiceBuilder implements ServiceBuilderInterface
{
    /** @var ServiceBuilderModuleInterface[] */
    private array $constructModules = [];

    /** @var ServiceBuilderModuleInterface[] */
    private array $processModules = [];

    public function __construct(array $constructModules = [], array $processModules = [])
    {
        array_walk($constructModules, [$this, 'addConstructModule']);
        array_walk($processModules, [$this, 'addProcessModule']);
    }

    protected function addConstructModule(ServiceBuilderModuleInterface $module): void
    {
        $this->constructModules[] = $module;
    }

    protected function addProcessModule(ServiceBuilderModuleInterface $module): void
    {
        $this->processModules[] = $module;
    }

    /**
     * @inheritDoc
     * @throws ConfigurationException
     */
    public function process(ServiceDraft $draft): void
    {
        /** @var ServiceSpec $spec */
        $spec = $draft->getSpec();

        /** @var ServiceBuilderModuleInterface $module */
        foreach($this->constructModules as $module) {
            if ($module->manages($spec)) {
                $module->process($draft);
            }
        }

        if ($draft->getService() === null) {
            throw new ConfigurationException("Unable to build service '{$spec->getName()}'.");
        }

        /** @var ServiceBuilderModuleInterface $module */
        foreach($this->processModules as $module) {
            if ($module->manages($spec)) {
                $module->process($draft);
            }
        }
    }
}
