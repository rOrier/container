<?php

namespace ROrier\Services\Components;

use Psr\Container\ContainerInterface;
use ROrier\Config\Interfaces\AnalyzerInterface;
use ROrier\Services\Interfaces\ServiceCallDelayerInterface;
use Exception;
use LogicException;

/**
 * Class ServiceCallDelayer
 */
class ServiceCallDelayer implements ServiceCallDelayerInterface
{
    private ContainerInterface $container;

    private AnalyzerInterface $analyzer;

    private array $delayedCalls = array();

    /**
     * ServiceCallDelayer constructor.
     * @param ContainerInterface $container
     * @param AnalyzerInterface $analyzer
     */
    public function __construct(
        ContainerInterface $container,
        AnalyzerInterface $analyzer
    ) {
        $this->container = $container;
        $this->analyzer = $analyzer;
    }

    /**
     * @throws Exception
     */
    public function callDelayed(): void
    {
        while (!empty($this->delayedCalls)) {
            $callDefinition = array_shift($this->delayedCalls);

            $this->executeCall($callDefinition['subject'], $callDefinition['name'], $callDefinition['call']);
        }
    }

    /**
     * @param string $name
     * @param array $calls
     * @param object $subject
     */
    public function addCalls(string $name, array $calls, ?object $subject = null): void
    {
        foreach ($calls as $call) {
            $this->addCall($name, $call, $subject);
        }
    }

    /**
     * @param string $name
     * @param array $call
     * @param object $subject
     */
    public function addCall(string $name, array $call, ?object $subject = null): void
    {
        $this->delayedCalls[] = array(
            'subject' => $subject,
            'name' => $name,
            'call' => $call
        );
    }

    /**
     * @param object $subject
     * @param string $name
     * @param array $delayedCall
     * @throws LogicException
     * @throws Exception
     */
    protected function executeCall($subject, $name, array $delayedCall)
    {
        if ($subject === null) {
            if (!$this->container->has($name)) {
                $message = "Unable to retrieve target service : '$name'.";
                throw new LogicException($message);
            }

            $service = $this->container->get($name);
        } else {
            $service = $subject;
        }

        if (!array_key_exists('method', $delayedCall)) {
            $message = "Target service call has no method name : '$name'.";
            throw new LogicException($message);
        }

        $method = $delayedCall['method'];
        $arguments = array();

        if (array_key_exists('arguments', $delayedCall)) {
            if (!is_array($delayedCall['arguments'])) {
                $message = "Target service call has inconsistent argument list : '$name::$method'.";
                throw new LogicException($message);
            }

            $arguments[] = $this->analyzer->parse($delayedCall['arguments']);
        }

        call_user_func_array(array($service, $method), $arguments);
    }
}
