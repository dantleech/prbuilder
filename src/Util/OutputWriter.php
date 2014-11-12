<?php

namespace PrBuilder\Util;

class OutputWriter
{
    private $outputClosure;

    public function __construct()
    {
        $this->outputClosure = function () {};
    }

    public function setOutputClosure(\Closure $outputClosure)
    {
        $this->outputClosure = $outputClosure;
    }

    public function write($message)
    {
        $out = $this->outputClosure;
        $out($message);
    }

    public function getClosure()
    {
        return $this->outputClosure;
    }
}
