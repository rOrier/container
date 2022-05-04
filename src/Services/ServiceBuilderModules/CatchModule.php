<?php

namespace ROrier\Container\Services\ServiceBuilderModules;

use ROrier\Container\Components\ServiceDraft;
use ROrier\Container\Components\ServiceSpec;
use ROrier\Config\Exceptions\ConfigurationException;
use ROrier\Container\Interfaces\ServiceBuilderModuleInterface;
use ROrier\Container\Interfaces\ServiceLibraryInterface;

class CatchModule implements ServiceBuilderModuleInterface
{
    private ServiceLibraryInterface $library;

    public function __construct(ServiceLibraryInterface $library)
    {
        $this->library = $library;
    }

    /**
     * @inheritDoc
     */
    public function manages(ServiceSpec $spec): bool
    {
        return (bool) $spec['catch'];
    }

    /**
     * @param ServiceDraft $draft
     */
    public function process(ServiceDraft $draft): void
    {
        $spec = $draft->getSpec();

        $catch = $this->buildCatchConfiguration($spec);

        $this->collectTaggedServices($catch, $draft);
    }

    /**
     * @param ServiceSpec $spec
     * @return array
     */
    protected function buildCatchConfiguration(ServiceSpec $spec)
    {
        $catch = $spec['catch'];

        if (!is_array($catch)) {
            $catch = array(
                'tag' => $catch,
                'method' => 'addServiceName',
                'built' => false
            );
        }

        assert(
            array_key_exists('tag', $catch) && !empty($catch['tag']),
            new ConfigurationException("Target service spec has catch option without 'tag' parameter : '{$spec->getName()}'.")
        );
        assert(
            array_key_exists('method', $catch) && !empty($catch['method']),
            new ConfigurationException("Target service spec has catch option without 'method' parameter : '{$spec->getName()}'.")
        );

        return $catch;
    }

    /**
     * @param array $catch
     * @param ServiceDraft $draft
     */
    protected function collectTaggedServices(array $catch, ServiceDraft $draft)
    {
        $built = array_key_exists('built', $catch) && ($catch['built'] === true);

        $findedTags = $this->library->getTaggedServices($catch['tag']);

        foreach ($findedTags as $findedTag) {
            $argument = $built ? '@' . $findedTag['service'] : $findedTag['service'];
            $arguments = array_merge(array($argument), $findedTag['options']);

            $draft->addCall($catch['method'], $arguments);
        }
    }
}
