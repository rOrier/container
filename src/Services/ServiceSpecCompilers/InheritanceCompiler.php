<?php

namespace ROrier\Container\Services\ServiceSpecCompilers;

use ROrier\Config\Exceptions\ConfigurationException;
use ROrier\Config\Tools\CollectionTool;
use ROrier\Container\Interfaces\CompilerInterface;

class InheritanceCompiler implements CompilerInterface
{
    private array $rawSpecs;

    /**
     * @param array $rawSpecs
     * @return array
     * @throws ConfigurationException
     */
    public function compile(array $rawSpecs): array
    {
        $this->rawSpecs = $rawSpecs;

        $specs = [];

        foreach($this->rawSpecs as $name => $definition) {
            $specs[$name] = $this->compileSpec($name);
        }

        return $specs;
    }

    /**
     * @param string $name
     * @return array
     * @throws ConfigurationException
     */
    protected function compileSpec(string $name)
    {
        if (!array_key_exists($name, $this->rawSpecs)) {
            throw new ConfigurationException("Unable to retrieve parent service '$name'.");
        }

        $rawSpec = $this->rawSpecs[$name];

        if (array_key_exists('inherit', $rawSpec)) {
            $parentRawSpec = $this->compileSpec($rawSpec['inherit']);

            if (array_key_exists('abstract', $parentRawSpec)) {
                unset($parentRawSpec['abstract']);
            }

            CollectionTool::merge($parentRawSpec, $rawSpec);

            $rawSpec = $parentRawSpec;
        }

        return $rawSpec;
    }
}
