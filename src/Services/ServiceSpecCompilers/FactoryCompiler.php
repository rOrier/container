<?php

namespace ROrier\Container\Services\ServiceSpecCompilers;

use ROrier\Config\Exceptions\ConfigurationException;
use ROrier\Container\Interfaces\CompilerInterface;

class FactoryCompiler implements CompilerInterface
{
    /**
     * @param array $rawSpecs
     * @return array
     * @throws ConfigurationException
     */
    public function compile(array $rawSpecs): array
    {
        array_walk($rawSpecs, [$this, 'expandFactoryConfiguration']);

        return $rawSpecs;
    }

    /**
     * @param array $rawSpec
     * @param string $name
     */
    protected function expandFactoryConfiguration(array &$rawSpec)
    {
        if (array_key_exists('factory', $rawSpec)) {
            $config = $rawSpec['factory'];

            if (!is_array($config)) {
                $config = array(
                    'service' => $config
                );
            }

            if (!array_key_exists('method', $config)) {
                $config['method'] = 'build';
            }

            $rawSpec['factory'] = $config;
        }
    }
}
