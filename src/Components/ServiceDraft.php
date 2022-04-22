<?php

namespace ROrier\Services\Components;

use Exception;

class ServiceDraft
{
    private ServiceSpec $spec;

    private ?object $service = null;

    public array $calls = [];

    public function __construct(ServiceSpec $spec)
    {
        $this->spec = $spec;
    }

    /**
     * @return ServiceSpec
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * @param object $service
     * @throws Exception
     */
    public function setService(object $service): void
    {
        if ($this->service !== null) {
            throw new Exception("Service already built : '{$this->getSpec()->getName()}'.");
        }

        $this->service = $service;
    }

    /**
     * @return object|null
     */
    public function getService(): ?object
    {
        return $this->service;
    }

    public function addCall(string $method, array $arguments = []): void
    {
        $this->calls[] = [
            'method' => $method,
            'arguments' => $arguments
        ];
    }

    /**
     * @return array
     */
    public function getCalls(): array
    {
        return $this->calls;
    }
}