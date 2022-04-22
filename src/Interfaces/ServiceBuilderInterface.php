<?php

namespace ROrier\Services\Interfaces;

use ROrier\Services\Components\ServiceDraft;

interface ServiceBuilderInterface
{
    /**
     * @param ServiceDraft $draft
     */
    public function process(ServiceDraft $draft): void;
}