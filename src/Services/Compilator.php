<?php

namespace ROrier\Services\Services;

use ROrier\Services\Interfaces\CompilatorInterface;
use ROrier\Services\Interfaces\CompilerInterface;

/**
 * Class Compilator
 */
class Compilator implements CompilatorInterface
{
    /** @var CompilerInterface[] */
    private array $compilers = [];

    /**
     * Compilator constructor.
     * @param CompilerInterface[] $compilers
     */
    public function __construct(array $compilers = [])
    {
        array_walk($compilers, [$this, 'addCompiler']);
    }

    /**
     * @param CompilerInterface $compiler
     */
    protected function addCompiler(CompilerInterface $compiler): void
    {
        $this->compilers[] = $compiler;
    }

    /**
     * @inheritDoc
     */
    public function compile(array $data): array
    {

        foreach ($this->compilers as $compiler) {
            $data = $compiler->compile($data);
        }

        return $data;
    }
}
