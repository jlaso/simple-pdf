<?php

namespace PHPfriends\SimplePdf\Measurement;

use FontLib\Font;

class FontMetrics
{
    const NEUTON = 'Neuton';

    const REGULAR = 'Regular';

    /** @var string */
    protected $fontName;
    /** @var string */
    protected $fontStyle;
    /** @var int[] */
    protected $widths;
    /** @var int */
    protected $firstChar = 32;
    /** @var int */
    protected $lastChar = 127;
    /** @var string */
    private $fontFile;

    /**
     * @param $fontName
     * @param $fontStyle
     */
    public function __construct($fontName, $fontStyle)
    {
        $this->fontName = $fontName;
        $this->fontStyle = $fontStyle;

        $this->fontFile = dirname(__DIR__) .
            sprintf('/Fonts/%s/%s-%s.ttf', $this->fontName, $this->fontName, $this->fontStyle);

        $this->font = Font::load($this->fontFile);

        $cacheDir = dirname(dirname(__DIR__)).'/cache/';
        $cachedFile = $cacheDir.sprintf('/%s-%s.cached', $this->fontName, $this->fontStyle);
        if(!file_exists($cachedFile)){
            $this->widths = $this->getCharMetrics();
            file_put_contents($cachedFile, json_encode($this->widths));
        }else {
            $this->widths = json_decode(file_get_contents($cachedFile), true);
        }
    }

    /**
     * @return string
     */
    public function getFontFile()
    {
        return $this->fontFile;
    }

    /**
     * @return \int[]
     */
    public function getWidths()
    {
        return $this->widths;
    }

    /**
     * extracted from https://github.com/PhenX/php-font-lib/blob/master/src/FontLib/AdobeFontMetrics.php
     * @return array
     */
    private function getCharMetrics()
    {
        $widths = [];

        $glyphIndexArray = $this->font->getUnicodeCharMap();

        if ($glyphIndexArray) {
            $hmtx = $this->font->getData("hmtx");

            foreach ($glyphIndexArray as $c => $g) {
                $seed = isset($hmtx[$g]) ? $hmtx[$g][0] : $hmtx[0][0];

                $widths['U' . $c] = $this->font->normalizeFUnit($seed);
            }
        }

        return $widths;
    }

}