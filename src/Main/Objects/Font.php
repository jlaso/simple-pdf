<?php

namespace PHPfriends\SimplePdf\Main\Objects;

class Font
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $style;

    /**
     * @param string $name
     * @param string $style
     */
    public function __construct($name, $style)
    {
        $this->name = $name;
        $this->style = $style;
    }

    /**
     * @return string
     */
    public function getFontName()
    {
        $font = str_replace(' ','_',trim($this->name));

        return sprintf("%s_%s", ucwords(strtolower($font)), strtolower($this->style));
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
}