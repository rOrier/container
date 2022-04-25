<?php

namespace ROrier\Container\Services;

use ROrier\Container\Exceptions\ContainerException;
use ROrier\Container\Interfaces\ContainerInterface;
use ROrier\Container\Interfaces\ServiceFactoryInterface;
use ROrier\Container\Interfaces\ServiceLibraryInterface;

/**
 * Class Container
 * @package PGSystem\Services
 */
class Container implements ContainerInterface
{
    private array $services = array();

    /** @var ServiceLibraryInterface */
    private ServiceLibraryInterface $library;

    /** @var ServiceFactoryInterface */
    private ServiceFactoryInterface $factory;

    /**
     * Container constructor.
     * @param ServiceLibraryInterface $library
     * @param ServiceFactoryInterface $factory
     */
    public function __construct(
        ServiceLibraryInterface $library,
        ServiceFactoryInterface $factory
    ) {
        $this->library = $library;
        $this->factory = $factory;
    }

    /**
     * @inheritDoc
     */
    public function get(string $id)
    {
        if (array_key_exists($id, $this->services)) {
            return $this->services[$id];
        } else {
            return $this->factory->build($id);
        }
    }

    /**
     * @inheritDoc
     */
    public function has(string $id) : bool
    {
        return $this->library->exists($id);
    }

    /**
     * @inheritDoc
     */
    public function exists(string $id) : bool
    {
        return array_key_exists($id, $this->services);
    }

    /**
     * @inheritDoc
     */
    public function setService(string $id, object $service): self
    {
        if (!$this->library->found($id)) {
            throw new ContainerException("Attempt to manually set a non-defined service : '$id'.");
        }

        $this->services[$id] = $service;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setServices(array $services): self
    {
        foreach ($services as $id => $service) {
            $this->setService($id, $service);
        }

        return $this;
    }

    public function reset(): self
    {
        $fixed_services = array();

        foreach ($this->services as $name => $service) {
            if ($this->library->isFixed($name)) {
                $fixed_services[$name] = $service;
            }
        }

        $this->services = $fixed_services;

        return $this;
    }
}
