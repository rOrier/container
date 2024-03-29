<?php

namespace ROrier\Container\Services\ServiceBuilderModules;

use ROrier\Config\Exceptions\ConfigurationException;
use ROrier\Config\Interfaces\AnalyzerInterface;
use ROrier\Container\Interfaces\ServiceBuilderModuleInterface;
use ROrier\Container\Components\ServiceDraft;
use ROrier\Container\Components\ServiceSpec;
use ROrier\Container\Exceptions\ContainerException;
use ROrier\Config\Foundations\AbstractParsingException;
use ROrier\Container\Interfaces\ContainerInterface;
use ROrier\Container\Interfaces\ServiceFactoryInterface;

class FactoryModule implements ServiceBuilderModuleInterface
{
    private ContainerInterface $container;

    private AnalyzerInterface $analyzer;

    /**
     * FactoryModule constructor.
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
     * @inheritDoc
     */
    public function manages(ServiceSpec $spec): bool
    {
        return isset($spec['factory']);
    }

    /**
     * @inheritDoc
     * @throws ContainerException
     * @throws AbstractParsingException
     */
    public function process(ServiceDraft $draft): void
    {
        $spec = $draft->getSpec();

        $service = call_user_func_array(
            $this->buildFactoryCallback($spec),
            $this->buildFactoryArguments($spec)
        );

        $draft->setService($service);
    }

    /**
     * @param ServiceSpec $spec
     * @return array
     * @throws AbstractParsingException
     */
    protected function buildFactoryArguments(ServiceSpec $spec): array
    {
        $arguments = $spec->getArguments();

        return $this->analyzer->parse($arguments);
    }

    /**
     * @param ServiceSpec $spec
     * @return array
     */
    protected function buildFactoryCallback(ServiceSpec $spec): array
    {
        assert(
            is_array($spec['factory']),
            new ConfigurationException("Malformed factory config for '{$spec->getName()}' : need Array format.")
        );
        assert(
            (bool) $spec['factory.service'],
            new ConfigurationException("Incomplete factory config for '{$spec->getName()}' : need 'service' key.")
        );
        assert(
            (bool) $spec['factory.method'],
            new ConfigurationException("Incomplete factory config for '{$spec->getName()}' : need 'method' key.")
        );

        return [
            $this->getFactory($spec['factory.service']),
            $spec['factory.method']
        ];
    }

    protected function getFactory(string $name): object
    {
        if ($this->container->exists($name)) {
            $factory = $this->container
                ->get($name)
            ;
        } else {
            $factory = $this->container
                ->get('factory.services')
                ->build($name, true)
            ;
        }

        return $factory;
    }
}
