<?php

namespace JLaso\SimplePdf\Parts;

class PdfInteger implements PartInterface
{
    protected $value;

    /**
     * @param int $value
     */
    public function __construct($value)
    {
        $this->value = intval($value);
    }

    public function dump()
    {
        return $this->value;
    }

}