<?php

namespace ROrier\Services\Components;

use Exception;
use ROrier\Services\Interfaces\ServiceCallDelayerInterface;
use ROrier\Services\Interfaces\ServiceWorkbenchInterface;
use ROrier\Services\Interfaces\ServiceBuilderInterface;

class ServiceWorkbench implements ServiceWorkbenchInterface
{
    private ServiceCallDelayerInterface $callDelayer;

    private array $lockedServices = array();

    private ServiceBuilderInterface $serviceBuilder;

    /**
     * ServiceWorkbench constructor.
     * @param ServiceBuilderInterface $serviceBuilder
     * @param ServiceCallDelayerInterface $callDelayer
     */
    public function __construct(
        ServiceBuilderInterface $serviceBuilder,
        ServiceCallDelayerInterface $callDelayer
    ) {
        $this->serviceBuilder = $serviceBuilder;
        $this->callDelayer = $callDelayer;
    }

    /**
     * @inheritDoc
     */
    public function preProcess(): void
    {
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function build(ServiceSpec $spec): object
    {
        if (in_array($spec->getName(), $this->lockedServices)) {
            throw new Exception("Circular reference detected for service : '{$spec->getName()}'.");
        }

        try {
            $this->lockServiceConstruction($spec);

            $draft = new ServiceDraft($spec);

            $this->serviceBuilder->process($draft);

            /** @var object|null $subject */
            $subject = $spec->isShared() ? null : $draft->getService();

            $this->callDelayer->addCalls(
                $spec->getName(),
                $draft->getCalls(),
                $subject
            );

            $this->finalizeServiceConstruction($spec);
        } catch (Exception $exception) {
            $this->finalizeServiceConstruction($spec);
            throw $exception;
        }

        return $draft->getService();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function postProcess(): void
    {
        $this->callDelayer->callDelayed();
    }

    protected function lockServiceConstruction(ServiceSpec $spec)
    {
        if ($spec->isShared()) {
            $this->lockedServices[] = $spec->getName();
        }
    }

    protected function unlockServiceConstruction(ServiceSpec $spec)
    {
        if ($spec->isShared()) {
            $index = array_search($spec->getName(), $this->lockedServices);
            unset($this->lockedServices[$index]);
        }
    }

    protected function finalizeServiceConstruction(ServiceSpec $spec)
    {
        $this->unlockServiceConstruction($spec);
    }
}
