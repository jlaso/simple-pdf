<?php

namespace PHPfriends\SimplePdf\Parts;

class PdfNumber implements PartInterface
{
    protected $value;

    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function dump()
    {
        return ''.$this->value;
    }

}