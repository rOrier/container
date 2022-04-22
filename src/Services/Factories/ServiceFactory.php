<?php

namespace ROrier\Services\Services\Factories;

use Exception;
use LogicException;
use ROrier\Services\Exceptions\ContainerException;
use ROrier\Services\Interfaces\ContainerInterface;
use ROrier\Services\Interfaces\ServiceFactoryInterface;
use ROrier\Services\Interfaces\ServiceLibraryInterface;
use ROrier\Services\Interfaces\ServiceWorkbenchBuilderInterface;
use ROrier\Services\Interfaces\ServiceWorkbenchInterface;

/**
 * Class ServiceFactory
 */
class ServiceFactory implements ServiceFactoryInterface
{
    private ContainerInterface $container;

    private ServiceLibraryInterface $library;

    private ServiceWorkbenchBuilderInterface $workbenchBuilder;

    private ?ServiceWorkbenchInterface $workbench = null;

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
    public function build(string $name): object
    {
        if ($this->workbench !== null) {
            return $this->buildService($name);
        } else {
            return $this->buildServiceChain($name);
        }
    }

    /**
     * @param string $name
     * @return object
     * @throws ContainerException
     */
    protected function buildServiceChain(string $name): object
    {
        try {
            $this->workbench = $this->workbenchBuilder->build();

            $this->workbench->preProcess();

            $service = $this->buildService($name);

            $this->workbench->postProcess();

            $this->cleaning();

            return $service;
        } catch (Exception $exception) {
            $this->cleaning();
            throw new ContainerException("Error during building service '$name'.", 0, $exception);
        }
    }

    protected function cleaning()
    {
        $this->workbench = null;
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

            $service = $this->workbench->build($spec);

            if ($spec->isShared()) {
                $this->container->setService($name, $service);
            }

        } catch (Exception $exception) {
            throw $exception;
        }

        return $service;
    }
}
