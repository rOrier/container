<?php

namespace ROrier\Services\Traits;

use ROrier\Config\Components\Bag;
use ROrier\Services\Interfaces\Services\ConfigurableServiceInterface;

trait ConfigurableServiceTrait
{
    protected Bag $config;

    public function __construct()
    {
        $this->config = new Bag();
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $config): ConfigurableServiceInterface
    {
        $this->config = new Bag($config);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addConfig(array $config): ConfigurableServiceInterface
    {
        $this->config->merge($config);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasConfig(string $key): bool
    {
        return isset($this->config[$key]);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(string $key)
    {
        return $this->config[$key];
    }
}