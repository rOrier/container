<?php

namespace ROrier\Services\Interfaces;

interface CompilerInterface
{
    /**
     * @param array $rawSpecs
     * @return array
     */
    public function compile(array $rawSpecs): array;
}