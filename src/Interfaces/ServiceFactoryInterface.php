<?php

namespace ROrier\Container\Interfaces;

interface ServiceFactoryInterface
{
    /**
     * @param string $name
     * @return object
     */
    public function build(string $name, bool $prioritary = false): object;
}