<?php

namespace ROrier\Container\Interfaces;

use ROrier\Container\Components\ServiceSpec;
use ROrier\Config\Exceptions\ConfigurationException;

interface ServiceLibraryInterface
{
    public function toArray(): array;

    /**
     * @param string $name
     * @return ServiceSpec
     * @throws ConfigurationException
     */
    public function getSpec($name): ServiceSpec;

    /**
     * @param string $name
     * @return bool
     */
    public function found($name): bool;

    /**
     * @param string $name
     * @return bool
     */
    public function exists($name): bool;

    /**
     * @param string $name
     * @return array
     */
    public function getTaggedServices(string $name): array;

    /**
     * @param string $name
     * @return bool
     */
    public function isFixed(string $name): bool;
}