<?php

namespace ROrier\Container\Interfaces;

interface ServiceCallDelayerInterface
{
    public function callDelayed(): void;

    /**
     * @param string $name
     * @param array $calls
     * @param object|null $subject
     */
    public function addCalls(string $name, array $calls, ?object $subject = null): void;

    /**
     * @param string $name
     * @param array $call
     * @param object|null $subject
     */
    public function addCall(string $name, array $call, ?object $subject = null): void;
}