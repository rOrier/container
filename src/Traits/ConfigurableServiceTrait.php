<?php

namespace ROrier\Container\Traits;

use ROrier\Config\Components\Bag;
use ROrier\Container\Interfaces\Services\ConfigurableServiceInterface;

trait ConfigurableServiceTrait
{
    protected Bag $config;

    protected function initConfigurableService()
    {
        trigger_error('Method ConfigurableServiceTrait::initConfigurableService is deprecated.', E_USER_DEPRECATED);
    }

    protected function initConfigBagIfNeeded()
    {
        if (!isset($this->config)) {
            $this->config = new Bag();
        }
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
        $this->initConfigBagIfNeeded();

        $this->config->merge($config);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasConfig(string $key): bool
    {
        $this->initConfigBagIfNeeded();

        return isset($this->config[$key]);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(string $key)
    {
        $this->initConfigBagIfNeeded();

        return $this->config[$key];
    }
}
