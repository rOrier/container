<?php

namespace ROrier\Container\Interfaces;

use ROrier\Container\Components\ServiceSpec;

interface CompilatorInterface
{
    /**
     * @param array $data
     * @return ServiceSpec[]
     */
    public function compile(array $data): array;
}