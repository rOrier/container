<?php

namespace ROrier\Services\Interfaces;

interface ServiceFactoryInterface
{
    /**
     * @param string $name
     * @return object
     */
    public function build(string $name): object;
}