<?php

namespace ROrier\Services\Components\Bootstraps;

use Exception;
use ROrier\Config\Tools\CollectionTool;
use ROrier\Services\Services\ServiceSpecCompilers\FactoryCompiler;
use ROrier\Services\Services\ServiceSpecCompilers\InheritanceCompiler;
use ROrier\Services\Services\Compilator;
use ROrier\Services\Services\Libraries\ServiceLibrary;

class LibraryBootstrap
{
    private array $data = [];

    private ?ServiceLibrary $library = null;

    /**
     * ContainerBootstrap constructor.
     */
    public function __construct()
    {
        $this->preloadData('fixed.json');
        $this->preloadData('abstract.json');
    }

    protected function preloadData($filename)
    {
        $path = realpath(__DIR__ . "/../../../config/services/$filename");

        $data = json_decode(file_get_contents($path), true);

        $this->addData($data);
    }

    /**
     * @param array $data
     * @return self
     */
    public function addData(array $data): self
    {
        CollectionTool::merge($this->data, $data);

        return $this;
    }

    /**
     * @return self
     */
    public function build(): self
    {
        $compilator = new Compilator([
            new InheritanceCompiler(),
            new FactoryCompiler()
        ]);

        $this->library = new ServiceLibrary($this->data, $compilator);

        return $this;
    }

    /**
     * @return ServiceLibrary
     * @throws Exception
     */
    public function get(): ServiceLibrary
    {
        if ($this->library === null) {
            throw new Exception("Service library has not yet been built.");
        }

        return $this->library;
    }
}
