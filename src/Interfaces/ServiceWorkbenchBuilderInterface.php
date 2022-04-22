<?php

namespace ROrier\Services\Interfaces;

interface ServiceWorkbenchBuilderInterface
{
    /**
     * @return ServiceWorkbenchInterface
     */
    public function build(): ServiceWorkbenchInterface;
}