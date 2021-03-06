<?php

namespace ROrier\Container\Interfaces;

use ROrier\Container\Components\ServiceSpec;

interface ServiceWorkbenchInterface
{
    public function preProcess(): void;

    /**
     * @param ServiceSpec $spec
     * @return object
     */
    public function build(ServiceSpec $spec): object;

    public function postProcess(): void;
}