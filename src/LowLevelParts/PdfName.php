<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

class PdfName implements PartInterface
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
    public function dump()
    {
        return '/'.$this->value;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return (string) $this->value;
    }
}