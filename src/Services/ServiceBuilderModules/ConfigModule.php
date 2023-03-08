<?php

namespace ROrier\Container\Services\ServiceBuilderModules;

use ROrier\Container\Components\ServiceSpec;
use ROrier\Config\Foundations\AbstractParsingException;
use ROrier\Config\Interfaces\AnalyzerInterface;
use ROrier\Container\Interfaces\ServiceBuilderModuleInterface;
use ROrier\Container\Components\ServiceDraft;
use ROrier\Container\Interfaces\Services\ConfigurableServiceInterface;

class ConfigModule implements ServiceBuilderModuleInterface
{
    private AnalyzerInterface $analyzer;

    /**
     * ConfigModule constructor.
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
        return (bool) $spec['config'];
    }

    /**
     * @inheritDoc
     * @throws AbstractParsingException
     */
    public function process(ServiceDraft $draft): void
    {
        $spec = $draft->getSpec();
        $service = $draft->getService();

        if ($draft->getService() instanceof ConfigurableServiceInterface) {
            $config = $spec['config'] ? $this->analyzer->parse($spec['config']) : [];
            $service->setConfig($config);
        }
    }
}
