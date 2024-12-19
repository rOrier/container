<?php

namespace ROrier\Container\Services;

use ArrayAccess;
use Countable;
use Iterator;
use Exception;
use LogicException;
use ROrier\Container\Interfaces\ContainerInterface;
use ROrier\Container\Interfaces\Services\ConfigurableServiceInterface;
use ROrier\Container\Traits\ConfigurableServiceTrait;

/**
 * Class Aggregator
 */
class Aggregator implements ConfigurableServiceInterface, ArrayAccess, Iterator, Countable
{
    use ConfigurableServiceTrait;

    /** @var ContainerInterface */
    private ContainerInterface $container;

    private array $index = array();

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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

    public function toArray(): array
    {
        $array = [];

        foreach($this->getNames() as $name) {
            $array[$name] = $this->getService($name);
        }

        return $array;
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
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->index);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->getService($offset);
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        throw new LogicException("Trying to set aggregator $offset with value : '$value'.");
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        throw new LogicException("Trying to unset aggregator $offset.");
    }

    public function count()
    {
        return count($this->index);
    }
}
