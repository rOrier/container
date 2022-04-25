<?php

namespace ROrier\Container;

use ROrier\Core\Foundations\AbstractPackage;

class ContainerPackage extends AbstractPackage
{
    protected const CONFIG_LOADER = 'ROrier\Core\Components\ConfigLoaders\JsonLoader';
}