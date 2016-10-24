<?php

namespace PHPfriends\SimplePdf\Main\Objects;

class Font
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $style;
    /** @var float */
    protected $size;

    /**
     * @param string $name
     * @param string $style
     * @param float $size
     */
    public function __construct($name, $style, $size)
    {
        $this->name = $name;
        $this->style = $style;
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Font
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param string $style
     * @return Font
     */
    public function setStyle($style)
    {
        $this->style = $style;
        return $this;
    }

    /**
     * @return float
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param float $size
     * @return Font
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }
}