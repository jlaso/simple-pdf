<?php

namespace PHPfriends\SimplePdf\Adaptor;

use PHPfriends\SimplePdf\Measurement\FontMetrics;
use PHPfriends\SimplePdf\LowLevelParts\Box;
use PHPfriends\SimplePdf\LowLevelParts\FontDescriptorDict;
use PHPfriends\SimplePdf\LowLevelParts\FontDict as FontDict;
use FontLib\Font as FontLibFont;
use PHPfriends\SimplePdf\LowLevelParts\FontFileStream;
use PHPfriends\SimplePdf\LowLevelParts\PdfName;
use PHPfriends\SimplePdf\LowLevelParts\PdfNumber;
use PHPfriends\SimplePdf\LowLevelParts\Widths;

class FontFile2FontDict
{
    /** @var FontDict */
    protected $fontDict;
    /** @var Widths */
    protected $widths;
    /** @var string */
    protected $encoding;
    /** @var FontLibFont */
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
    protected $cmap;
    protected $fixedWidth;

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
        $min = 1000; $max = 0;
        foreach($widths as $width){
            $min = min($min, $width);
            $max = max($max, $widths);
        }
        $this->fixedWidth = ($max == $min);

        $this->baseName = $fontTool->getBasename();
        $this->fontBBox = $fontTool->getFontBBox();
        $this->ascent = $fontTool->getAscender();
        $this->descent = $fontTool->getDescender();
        $this->italicAngle = $fontTool->getItalicAngle();
        $this->cmap = $fontTool->getCmap();
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

        // @TODO extract the right CapHeight from the font folder, this is an approach only
        $height = abs($this->fontBBox['yMax'] /*- $this->fontBBox['yMin']*/);
        $fdd->addItem('CapHeight', new PdfNumber($height));

        // @TODO extract the right StemV from the font file
        $fdd->addItem('StemV', new PdfNumber(0));

        $fdd->addItem('Flags', new PdfNumber($this->calcFlags()));

        $fdd->addItem('FontFile2', new FontFileStream($this->fontFile));

        return $fdd;
    }

    /**
     * //@TODO extract the rights flags from the font file
     *
     * @return int
     */
    private function calcFlags()
    {
        $flags = [
            'FixedPitch'    => 0b0000000010,  //  2
            'Serif'         => 0b0000000100,  //  4
            'Symbolic'      => 0b0000001000,  //  8
            'Script'        => 0b0000010000,  // 16
            'Nonsymbolic'   => 0b0000100000,  // 32
            'Italic'        => 0b0001000000,  // 64
            'AllCap'        => 0b0010000000,  //128
            'SmallCap'      => 0b0100000000,  //256
            'ForceBold'     => 0b1000000000,  //512
        ];
        $result = 0;

        if ($this->widths->getLength() > (255-32+1)) {
            $result |= $flags['Symbolic'];
        }else {
            $result |= $flags['Nonsymbolic'];
        }

        if ($this->fixedWidth) {
             $result |= $flags['FixedPitch'];
        }

        if ($this->italicAngle > 200) {
            $result |= $flags['Italic'];
        }

        return $result;
    }
}