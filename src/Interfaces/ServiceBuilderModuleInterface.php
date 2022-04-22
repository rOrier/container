<?php

namespace ROrier\Services\Interfaces;

use ROrier\Services\Components\ServiceDraft;
use ROrier\Services\Components\ServiceSpec;

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