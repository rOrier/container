<?php

namespace ROrier\Container\Services\DelayedProxies;

use ROrier\Container\Exceptions\ContainerException;
use ROrier\Container\Interfaces\ContainerInterface;

/**
 * Class ContainerProxy
 */
class ContainerProxy implements ContainerInterface
{
    private ?ContainerInterface $container = null;

    /**
     * @return ContainerInterface
     * @throws ContainerException
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            throw new ContainerException("Service container was not provided.");
        }

        return $this->container;
    }

    /**
     * @param ContainerInterface $parameters
     */
    public function setContainer(ContainerInterface $parameters): void
    {
        $this->container = $parameters;
    }

    /**
     * @inheritDoc
     * @throws ContainerException
     */
    public function get($var)
    {
        return $this->getContainer()->get($var);
    }

    /**
     * @inheritDoc
     * @throws ContainerException
     */
    public function has(string $id): bool
    {
        return $this->getContainer()->has($id);
    }

    /**
     * @inheritDoc
     * @throws ContainerException
     */
    public function exists(string $id)
    {
        return $this->getContainer()->exists($id);
    }

    /**
     * @inheritDoc
     * @throws ContainerException
     */
    public function setService(string $name, object $service): self
    {
        $this->getContainer()->setService($name, $service);

        return $this;
    }

    /**
     * @inheritDoc
     * @throws ContainerException
     */
    public function setServices(array $services): self
    {
        $this->getContainer()->setServices($services);

        return $this;
    }

    /**
     * @inheritDoc
     * @throws ContainerException
     */
    public function reset(): self
    {
        $this->getContainer()->reset();

        return $this;
    }
}
