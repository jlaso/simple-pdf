<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

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

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }
}