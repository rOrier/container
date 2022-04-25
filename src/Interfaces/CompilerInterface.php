<?php

namespace ROrier\Container\Interfaces;

interface CompilerInterface
{
    /**
     * @param array $rawSpecs
     * @return array
     */
    public function compile(array $rawSpecs): array;
}