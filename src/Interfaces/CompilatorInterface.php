<?php

namespace ROrier\Services\Interfaces;

use ROrier\Services\Components\ServiceSpec;

interface CompilatorInterface
{
    /**
     * @param array $data
     * @return ServiceSpec[]
     */
    public function compile(array $data): array;
}