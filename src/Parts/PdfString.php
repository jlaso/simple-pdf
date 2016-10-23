<?php

namespace JLaso\SimplePdf\Parts;

class PdfString implements PartInterface
{
    protected $value;

    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = (string) $value;
    }

    public function dump()
    {
        return '('.$this->value.')';
    }

}