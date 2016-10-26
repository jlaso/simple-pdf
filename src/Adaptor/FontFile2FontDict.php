<?php

namespace PHPfriends\SimplePdf\Adaptor;

use PHPfriends\SimplePdf\Measurement\FontMetrics;
use PHPfriends\SimplePdf\Parts\FontDict as FontDict;
use FontLib\Font as FontFile;
use PHPfriends\SimplePdf\Parts\Widths;

class FontFile2FontDict
{
    /** @var FontDict */
    protected $fontDict;
    /** @var Widths */
    protected $widths;
    /** @var string */
    protected $encoding;
    /** @var FontFile */
    protected $fontFile;
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

    private function process()
    {
        $fontTool = new FontMetrics($this->name, $this->style);

        $widths = $fontTool->getWidths();

        print_r($widths);
    }

    /**
     * @return FontDict
     */
    public function getFontDict()
    {
        return $this->fontDict;
    }

    /**
     * @return Widths
     */
    public function getWidths()
    {
        return $this->widths;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

}