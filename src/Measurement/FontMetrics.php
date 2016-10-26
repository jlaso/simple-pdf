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
    /** @var array */
    protected $os2;

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

        $cacheDir = dirname(dirname(__DIR__)) . '/cache/';
        $cachedFile = $cacheDir . sprintf('/%s-%s.cached', $this->fontName, $this->fontStyle);
        if (!file_exists($cachedFile)) {
            $this->widths = $this->getCharMetrics();
            file_put_contents($cachedFile, json_encode($this->widths));
        } else {
            $this->widths = json_decode(file_get_contents($cachedFile), true);
        }

        $this->os2 = $this->font->getData("OS/2");
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

    /**
     * @return null|string
     */
    public function getBasename()
    {
        return $this->font->getFontPostscriptName();
    }

    public function getFontBBox()
    {
        $head = $this->font->getData("head");

        return [
            'xMin' => $this->font->normalizeFUnit($head["xMin"]),
            'yMin' => $this->font->normalizeFUnit($head["yMin"]),
            'xMax' => $this->font->normalizeFUnit($head["xMax"]),
            'yMax' => $this->font->normalizeFUnit($head["yMax"]),
        ];
    }

    public function getAscender()
    {
        $hhea = $this->font->getData("hhea");
        if (isset($hhea["ascent"])) {
            return $this->font->normalizeFUnit($hhea["ascent"]);
        }
        return $this->font->normalizeFUnit($this->os2["typoAscender"]);
    }

    public function getDescender()
    {
        $hhea = $this->font->getData("hhea");
        if (isset($hhea["descent"])) {
            return $this->font->normalizeFUnit($hhea["descent"]);
        }
        return $this->font->normalizeFUnit($this->os2["typoDescender"]);
    }

    public function getItalicAngle()
    {
        $post = $this->font->getData("post");

        return $post["italicAngle"];
    }

}