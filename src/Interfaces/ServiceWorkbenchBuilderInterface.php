<?php

namespace ROrier\Container\Interfaces;

interface ServiceWorkbenchBuilderInterface
{
    /**
     * @return ServiceWorkbenchInterface
     */
    public function build(): ServiceWorkbenchInterface;
}