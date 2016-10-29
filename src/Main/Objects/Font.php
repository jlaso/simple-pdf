<?php

namespace PHPfriends\SimplePdf\Main\Objects;

use PHPfriends\SimplePdf\Adaptor\FontManager;
use PHPfriends\SimplePdf\Measurement\FontMetrics;

class Font
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $style;
    /** @var float */
    protected $fontHeight;
    /** @var string */
    protected $basename;

    /**
     * @param string $name
     * @param string $style
     */
    public function __construct($name, $style)
    {
        $this->name = $name;
        $this->style = $style;
        $fm = new FontMetrics($name, $style);
        $this->fontHeight = $fm->getFontHeight('bbox');
        $this->basename = $fm->getBasename();
    }

    /**
     * @return string
     */
    public function getFontName()
    {
        $font = str_replace(' ','-',ucwords(trim($this->name)));

        return sprintf("%s-%s", $font, ucwords($this->style));
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
     * @param float $fontSize
     * @return float
     */
    public function getFontHeight($fontSize)
    {
        return round($this->fontHeight * $fontSize / 1000, 4);
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return FontManager::getInstance()->addFontName($this->basename);
    }
}