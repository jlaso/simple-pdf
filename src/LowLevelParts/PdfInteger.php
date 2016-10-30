<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

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

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }
}