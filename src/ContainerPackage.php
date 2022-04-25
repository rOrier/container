<?php

namespace ROrier\Container;

use ROrier\Core\Foundations\AbstractPackage;

class ContainerPackage extends AbstractPackage
{
    protected const CONFIG_LOADER = 'ROrier\Core\Components\ConfigLoaders\JsonLoader';

    /**
     * @inheritDoc
     */
    public function getConfigPath(): string
    {
        return realpath($this->getRoot() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config');
    }
}