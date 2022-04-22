<?php

namespace ROrier\Services\Services\ServiceBuilderModules;

use ROrier\Services\Components\ServiceDraft;
use ROrier\Services\Components\ServiceSpec;
use ROrier\Config\Exceptions\ConfigurationException;
use ROrier\Services\Interfaces\ServiceBuilderModuleInterface;

class CallsModule implements ServiceBuilderModuleInterface
{
    /**
     * @inheritDoc
     */
    public function manages(ServiceSpec $spec): bool
    {
        return (bool) $spec['calls'];
    }

    /**
     * @param ServiceDraft $draft
     */
    public function process(ServiceDraft $draft): void
    {
        $spec = $draft->getSpec();

        assert(
            is_array($spec['calls']),
            new ConfigurationException("Service '{$spec->getName()}' contains malformed calls.")
        );

        foreach($spec['calls'] as $call) {
            assert(
                is_array($call) && array_key_exists('method', $call),
                new ConfigurationException("Service '{$spec->getName()}' contains malformed call.")
            );

            $method = $call['method'];
            $arguments = array_key_exists('arguments', $call) ? $call['method'] : [];

            $draft->addCall($method, $arguments);
        }
    }
}
