<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

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

    /**
     * @return string
     */
    function __toString()
    {
        return (string) $this->value;
    }

    public function dump()
    {
        return '('.$this->value.')';
    }

}