<?php

namespace ROrier\Container\Services\Factories;

use Exception;
use LogicException;
use ROrier\Container\Exceptions\ContainerException;
use ROrier\Container\Interfaces\ContainerInterface;
use ROrier\Container\Interfaces\ServiceFactoryInterface;
use ROrier\Container\Interfaces\ServiceLibraryInterface;
use ROrier\Container\Interfaces\ServiceWorkbenchBuilderInterface;
use ROrier\Container\Interfaces\ServiceWorkbenchInterface;

/**
 * Class ServiceFactory
 */
class ServiceFactory implements ServiceFactoryInterface
{
    private ContainerInterface $container;

    private ServiceLibraryInterface $library;

    private ServiceWorkbenchBuilderInterface $workbenchBuilder;

    /** @var ServiceWorkbenchInterface[] */
    private array $workbench = [];

    /**
     * ServiceFactory constructor.
     * @param ContainerInterface $container
     * @param ServiceLibraryInterface $library
     * @param ServiceWorkbenchBuilderInterface $workbenchBuilder
     */
    public function __construct(
        ContainerInterface $container,
        ServiceLibraryInterface $library,
        ServiceWorkbenchBuilderInterface $workbenchBuilder
    ) {
        $this->container = $container;
        $this->library = $library;
        $this->workbenchBuilder = $workbenchBuilder;
    }

    /**
     * @return ServiceWorkbenchBuilderInterface
     */
    public function getWorkbenchBuilder(): ServiceWorkbenchBuilderInterface
    {
        return $this->workbenchBuilder;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function build(string $name, bool $prioritary = false): object
    {
        if (empty($this->workbench) || $prioritary) {
            return $this->buildServiceChain($name);
        } else {
            return $this->buildService($name);
        }
    }

    /**
     * @return ServiceWorkbenchInterface
     */
    public function getWorkbench(): object
    {
        return end($this->workbench);
    }

    /**
     * @param string $name
     * @return object
     * @throws ContainerException
     */
    protected function buildServiceChain(string $name): object
    {
        try {
            $this->workbench[] = $this->workbenchBuilder->build();

            $this->getWorkbench()->preProcess();

            $service = $this->buildService($name);

            $this->getWorkbench()->postProcess();

            $this->cleaning();

            return $service;
        } catch (Exception $exception) {
            $this->cleaning();
            throw new ContainerException("Error during building service '$name' : " . $exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    protected function cleaning()
    {
        array_pop($this->workbench);
    }

    /**
     * @param string $name
     * @return object
     * @throws LogicException
     * @throws Exception
     */
    protected function buildService(string $name): object
    {
        if ($this->container->exists($name)) {
            return $this->container->get($name);
        } elseif (!$this->library->found($name)) {
            throw new LogicException("Call to a non-existant service : '$name'.");
        } elseif (!$this->library->exists($name)) {
            throw new LogicException("Unable to create abstract service : '$name'.");
        }

        try {
            $spec = $this->library->getSpec($name);

            $service = $this->getWorkbench()->build($spec);

            if ($spec->isShared()) {
                $this->container->setService($name, $service);
            }

        } catch (Exception $exception) {
            throw $exception;
        }

        return $service;
    }
}
