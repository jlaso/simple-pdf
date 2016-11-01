<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

class PdfDate implements PartInterface
{
    /** @var \DateTime */
    protected $date;

    /**
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        $this->date = new \DateTime($value);
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->date->format('Y-m-d H:i:j');
    }

    public function dump()
    {
        return '(D:'.$this->date->format('YmdHij').'Z00\'00\')';
    }

}