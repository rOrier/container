<?php

namespace ROrier\Services\Services;

use ArrayAccess;
use Iterator;
use Exception;
use LogicException;
use ROrier\Services\Interfaces\ContainerInterface;
use ROrier\Services\Interfaces\Services\ConfigurableServiceInterface;
use ROrier\Services\Traits\ConfigurableServiceTrait;

/**
 * Class Aggregator
 */
class Aggregator implements ConfigurableServiceInterface, ArrayAccess, Iterator
{
    use ConfigurableServiceTrait {
        ConfigurableServiceTrait::__construct as private configurableServiceConstruct;
    }

    /** @var ContainerInterface */
    private ContainerInterface $container;

    private array $index = array();

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->configurableServiceConstruct();
    }

    /**
     * @param string $serviceName
     * @param string|null $name
     * @throws Exception
     */
    public function addServiceName(string $serviceName, string $name = null)
    {
        $type = $this->config['prefix'];
        $quotedType = preg_quote($type);

        if ($name === null) {
            if (preg_match("/^$quotedType\\.(?P<name>.+)/", $serviceName, $result)) {
                $name = $result['name'];
            } else {
                throw new Exception(
                    "Unable to automatically determine the $type name with the service name : '$serviceName'."
                );
            }
        }

        $this->index[$name] = $serviceName;
    }

    /**
     * @param string $name
     * @return object
     * @throws Exception
     */
    public function getService(string $name)
    {
        $interface = $this->config['interface'];

        $serviceName = $this->getServiceName($name);

        /** @var object $service */
        $service = $this->container->get($serviceName);

        if ($interface) {
            if (!$service instanceof $interface) {
                $class = get_class($service);
                $text = "Service '$serviceName' is not a valid {$this->config['prefix']}.";
                $text .= " '$interface' is required.";
                $text .= " Instance of '$class' found.";
                throw new Exception($text);
            }
        }

        return $service;
    }

    public function getServiceName(string $name)
    {
        if (!$this->offsetExists($name)) {
            throw new LogicException("Unknown {$this->config['prefix']} name : '$name'.");
        }

        return $this->index[$name];
    }

    public function getNames()
    {
        return array_keys($this->index);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function current()
    {
        return $this->getService($this->key());
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        next($this->index);
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return key($this->index);
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return key($this->index) !== null;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        reset($this->index);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->index);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function offsetGet($offset)
    {
        return $this->getService($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        throw new LogicException("Trying to set aggregator $offset with value : '$value'.");
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        throw new LogicException("Trying to unset aggregator $offset.");
    }
}
