<?php

namespace ROrier\Services\Services\ServiceBuilderModules;

use ROrier\Services\Components\ServiceSpec;
use ROrier\Config\Foundations\AbstractParsingException;
use ROrier\Config\Interfaces\AnalyzerInterface;
use ROrier\Services\Interfaces\ServiceBuilderModuleInterface;
use ROrier\Services\Components\ServiceDraft;
use ROrier\Services\Interfaces\Services\ConfigurableServiceInterface;

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

        if (($draft->getService() instanceof ConfigurableServiceInterface) && $spec['config']) {
            $config = $this->analyzer->parse($spec['config']);
            $service->setConfig($config);
        }
    }
}
