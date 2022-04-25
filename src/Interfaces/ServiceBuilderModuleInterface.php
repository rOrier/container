<?php

namespace ROrier\Container\Interfaces;

use ROrier\Container\Components\ServiceDraft;
use ROrier\Container\Components\ServiceSpec;

interface ServiceBuilderModuleInterface
{
    /**
     * @param ServiceSpec $spec
     * @return bool
     */
    public function manages(ServiceSpec $spec): bool;

    /**
     * @param ServiceDraft $draft
     */
    public function process(ServiceDraft $draft): void;
}