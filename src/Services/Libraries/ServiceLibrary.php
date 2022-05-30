<?php

namespace ROrier\Container\Services\Libraries;

use ROrier\Container\Components\ServiceSpec;
use ROrier\Config\Exceptions\ConfigurationException;
use ROrier\Container\Interfaces\CompilatorInterface;
use ROrier\Container\Interfaces\ServiceLibraryInterface;

/**
 * Class ServiceLibrary
 */
class ServiceLibrary implements ServiceLibraryInterface
{
    private ?CompilatorInterface $compilator;

    /** @var ServiceSpec[] */
    private array $specs = [];

    /**
     * ServiceLibrary constructor.
     * @param CompilatorInterface $compilator
     * @param array $rawSpecs
     */
    public function __construct(array $rawSpecs = [], ?CompilatorInterface $compilator = null)
    {
        $this->compilator = $compilator;

        $this->specs = $this->buildSpecs($rawSpecs);
    }

    /**
     * @param array $rawSpecs
     * @return ServiceSpec[]
     */
    protected function buildSpecs(array $rawSpecs): array
    {
        if ($this->compilator !== null) {
            $rawSpecs = $this->compilator->compile($rawSpecs);
        }

        $specs = [];

        foreach($rawSpecs as $name => $rawSpec) {
            $specs[$name] = new ServiceSpec($name, $rawSpec);
        }

        return $specs;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $data = [];

        /** @var ServiceSpec $spec */
        foreach($this->specs as $name => $spec) {
            $data[$name] = $spec->toArray();
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getCompilator(): ?CompilatorInterface
    {
        return $this->compilator;
    }

    /**
     * @inheritDoc
     */
    public function getTaggedServices($names): array
    {
        $tags = array();

        if (!is_array($names)) {
            $names = [$names];
        }

        foreach ($names as $name) {
            foreach ($this->specs as $spec) {
                if (!$spec->isAbstract()) {
                    $tags = array_merge($tags, $spec->getTagDefinitions($name));
                }
            }
        }

        return $tags;
    }

    /**
     * @inheritDoc
     */
    public function getSpec($name): ServiceSpec
    {
        if (!$this->found($name)) {
            throw new ConfigurationException("Service spec not found : '$name'.");
        }

        return $this->specs[$name];
    }

    /**
     * @inheritDoc
     */
    public function found($name): bool
    {
        return array_key_exists($name, $this->specs);
    }

    /**
     * @inheritDoc
     */
    public function exists($name): bool
    {
        return ($this->found($name) && !$this->specs[$name]->isAbstract());
    }

    /**
     * @inheritDoc
     * @throws ConfigurationException
     */
    public function isFixed(string $name): bool
    {
        return $this->getSpec($name)->isFixed();
    }

    /**
     * @inheritDoc
     */
    public function getFixedServices(): array
    {
        $names = array();

        /** @var ServiceSpec $spec */
        foreach ($this->specs as $spec) {
            if ($spec['fixed']) {
                $names[] = $spec->getName();
            }
        }

        return $names;
    }
}
