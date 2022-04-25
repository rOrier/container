<?php

namespace ROrier\Container\Interfaces;

use Psr\Container\ContainerInterface as PSRContainerInterface;
use ROrier\Container\Exceptions\ContainerException;

/**
 * Interface ParametersInterface
 */
interface ContainerInterface extends PSRContainerInterface
{
    /**
     * Return TRUE if entry is already instantiated.
     * @param string $id Identifier of the entry to look for.
     * @return bool
     */
    public function exists(string $id);

    /**
     * @param string $id
     * @param object $service
     * @return self
     * @throws ContainerException
     */
    public function setService(string $id, object $service): ContainerInterface;

    /**
     * @param object[] $services
     * @return self
     * @throws ContainerException
     */
    public function setServices(array $services): ContainerInterface;

    /**
     * @return self
     */
    public function reset(): ContainerInterface;
}
