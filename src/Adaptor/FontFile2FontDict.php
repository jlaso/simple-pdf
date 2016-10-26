<?php

namespace PHPfriends\SimplePdf\Adaptor;

use PHPfriends\SimplePdf\Measurement\FontMetrics;
use PHPfriends\SimplePdf\Parts\Box;
use PHPfriends\SimplePdf\Parts\FontDescriptorDict;
use PHPfriends\SimplePdf\Parts\FontDict as FontDict;
use FontLib\Font as FontFile;
use PHPfriends\SimplePdf\Parts\PdfName;
use PHPfriends\SimplePdf\Parts\PdfNumber;
use PHPfriends\SimplePdf\Parts\PdfString;
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
    /** @var string */
    protected $baseName;
    /** @var array */
    protected $fontBBox;
    protected $ascent;
    protected $descent;
    protected $italicAngle;

    /**
     * @param string $name
     * @param string $style
     */
    public function __construct($name, $style)
    {
        $this->name = $name;
        $this->style = $style;

        $fontTool = new FontMetrics($this->name, $this->style);
        $this->fontFile = $fontTool->getFontFile();

        $widths = $fontTool->getWidths();
        $this->widths = new Widths($widths);

        $this->baseName = $fontTool->getBasename();
        $this->fontBBox = $fontTool->getFontBBox();
        $this->ascent = $fontTool->getAscender();
        $this->descent = $fontTool->getDescender();
        $this->italicAngle = $fontTool->getItalicAngle();
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

    /**
     * @return string
     */
    public function getBaseName()
    {
        return $this->baseName;
    }

    /**
     * @return FontDescriptorDict
     */
    public function getFontDescriptor()
    {
        $fdd = new FontDescriptorDict($this->fontFile);
        $fdd->addItem('FontName', new PdfName($this->baseName));
        $bbox = new Box(
            $this->fontBBox['xMin'],
            $this->fontBBox['yMin'],
            $this->fontBBox['xMax'],
            $this->fontBBox['yMax']
        );
        $fdd->addItem('FontBBox', $bbox);
        $fdd->addItem('ItalicAngle', new PdfNumber($this->italicAngle));
        $fdd->addItem('Ascent', new PdfNumber($this->ascent));
        $fdd->addItem('Descent', new PdfNumber($this->descent));
        $fdd->addItem('CapHeight', new PdfNumber(0));
        $fdd->addItem('StemV', new PdfNumber(0));
        $fdd->addItem('Flags', new PdfNumber(0));

        return $fdd;
    }
}