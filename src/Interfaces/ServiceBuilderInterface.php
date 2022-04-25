<?php

namespace ROrier\Container\Interfaces;

use ROrier\Container\Components\ServiceDraft;

interface ServiceBuilderInterface
{
    /**
     * @param ServiceDraft $draft
     */
    public function process(ServiceDraft $draft): void;
}