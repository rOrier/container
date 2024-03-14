<?php

namespace ROrier\Container\Interfaces\Services;

/**
 * Interface ConfigurableServiceInterface
 * @package PGSystem\Interfaces\Services
 */
interface ConfigurableServiceInterface
{
    /**
     * @param array $config
     * @return self
     */
    public function setConfig(array $config): ConfigurableServiceInterface;

    /**
     * @param array $config
     * @return self
     */
    public function addConfig(array $config): ConfigurableServiceInterface;

    /**
     * @param string $key
     * @return mixed
     */
    public function getConfig(string $key);

    /**
     * @param string $key
     * @return bool
     */
    public function hasConfig(string $key): bool;

    /**
     * @return array
     */
    public function exportConfig(): array;
}
