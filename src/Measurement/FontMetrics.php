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
    /** @var string */
    protected $baseName;

    // https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6OS2.html
    // The 'OS/2' table consists of a set of metrics that are required by and Windows
    protected $os2;

    // https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6hmtx.html
    // The 'hmtx' table contains metric information for the horizontal layout each of the glyphs in the font.
    protected $hmtx;

    // https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6head.html
    // The 'head' table contains global information about the font.
    protected $head;

    // https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6hhea.html
    // The 'hhea' table contains information needed to layout fonts whose characters are written horizontally
    protected $hhea;

    // https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6cmap.html
    // The 'cmap' table maps character codes to glyph indices
    protected $cmap;

    // https://developer.apple.com/fonts/TrueType-Reference-Manual/RM06/Chap6post.html
    // The 'post' table contains information needed to use a TrueType font on a PostScript printer
    protected $post;

    protected $fontBBox;

    // Distance from baseline of highest ascender
    protected $ascent;

    // Distance from baseline of lowest descender
    protected $descent;

    // Italic angle in degrees
    protected $italicAngle;

    // typographic line gap
    protected $heightOffset;

    // 	Underline thickness
    protected $underlineThickness;

    // 	Underline position
    protected $underlinePosition;

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

        $cacheDir = dirname(dirname(__DIR__)) . '/cache/';
        $cachedFile = $cacheDir . sprintf('/%s-%s.php', $this->fontName, $this->fontStyle);

        if (!file_exists($cachedFile) || (filemtime($this->fontFile) != filemtime($cachedFile))) {

            $this->font = Font::load($this->fontFile);
            $this->createCacheFile($cachedFile);
            touch($cachedFile, filemtime($this->fontFile ));

        }

        $cachedData = include($cachedFile);
        foreach($cachedData['keys'] as $property){
            $this->{$property} = $cachedData['data'][$property];
        }
        unset($cachedData);
    }

    /**
     * @param string $dest   file
     */
    private function createCacheFile($dest)
    {
        $script = [];

        $add = function($name, $data) use (&$script){
            $script[] = sprintf('$keys[] = "%s";', $name);
            $script[] = sprintf('$data["%s"] = %s;', $name, var_export($data, true));
        };

        $script[] = "<?php\r\n// Created on ".date("Y-m-d")."\r\n";
        $fontFile = str_replace(dirname(dirname(__DIR__)), '', $this->fontFile);
        $script[] = "// FONT: ".$this->fontName.'-'.$this->fontStyle."\r\n";
        $script[] = "// fontFile: ".$fontFile."\r\n";
        $script[] = '$data=[]; $keys=[];';
        $script[] = '';
        $add('widths', $this->getCharMetrics());
        $os2 = $this->font->getData('OS/2');
        $add('os2', $os2);
        $add('hmtx', $this->font->getData('hmtx'));
        $add('baseName', $this->font->getFontPostscriptName());
        $add('copyright', $this->font->getFontCopyright());
        $add('fontType', $this->font->getFontType());
        $add('subFamily', $this->font->getFontSubfamily());
        $add('fontFullName', $this->font->getFontFullName());
        $add('fontVersion', $this->font->getFontVersion());
        $add('fontWeight', $this->font->getFontWeight());
        $add('subset', $this->font->getSubset());
        $head = $this->font->getData('head');
        $add('head', $head);
        $fontBBox = [
            'xMin' => $this->font->normalizeFUnit($head['xMin']),
            'yMin' => $this->font->normalizeFUnit($head['yMin']),
            'xMax' => $this->font->normalizeFUnit($head['xMax']),
            'yMax' => $this->font->normalizeFUnit($head['yMax']),
        ];
        $add('fontBBox', $fontBBox);
        $hhea = $this->font->getData('hhea');
        $add('hhea', $hhea);
        $add(
            'ascent',
            $this->font->normalizeFUnit(
                isset($hhea['ascent']) ? $hhea['ascent'] : $os2['typoAscender']
            )
        );
        $add(
            'descent',
            $this->font->normalizeFUnit(
                isset($hhea['descent']) ? $hhea['descent'] : $os2['typoDescender']
            )
        );
        $add(
            'heightOffset',
            $this->font->normalizeFUnit(
                isset($hhea['descent']) ? $hhea["lineGap"] : $os2["typoLineGap"]
            )
        );
        $post = $this->font->getData('post');
        $add('post', $post);
        $add('italicAngle', $post['italicAngle']);
        $add('cmap', $this->font->getData('cmap'));
        $add('isFixedPitch', ($post['isFixedPitch'] ? true : false));
        $add('underlineThickness', $this->font->normalizeFUnit($post['underlineThickness']));
        $add('underlinePosition', $this->font->normalizeFUnit($post['underlinePosition']));

        $script[] = '';
        $script[] = 'return ["data" => $data, "keys" => $keys];';

        file_put_contents($dest, join("\r\n", $script));
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
    public function getWidths($from = 32, $to = 255)
    {
        $result = [];
        for($i = $from; $i <= $to; $i++){
            $result[$i] = isset($this->widths[''.$i]) ? $this->widths['' . $i] : 0;
        }

        return $result;
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

                $widths['' . $c] = $this->font->normalizeFUnit($seed);
            }
        }

        return $widths;
    }

    /**
     * @return null|string
     */
    public function getBasename()
    {
        return $this->baseName;
    }

    /**
     * @return array
     */
    public function getFontBBox()
    {
        return $this->fontBBox;
    }

    /**
     * @return float
     */
    public function getAscender()
    {
        return $this->ascent;
    }

    /**
     * @return float
     */
    public function getDescender()
    {
        return $this->descent;
    }

    /**
     * @return mixed
     */
    public function getItalicAngle()
    {
        return $this->italicAngle;
    }

    public function getCmap()
    {
        return $this->cmap;
    }

    /**
     * @param string $type
     * @return float
     */
    public function getFontHeight($type = 'cap')
    {
        switch ($type){
            case 'cap':
                // @TODO, no idea so far how to get that
                break;

            case 'offset':
                return $this->heightOffset;
                break;

            case 'bbox':
                return (abs($this->fontBBox['yMin'] - $this->fontBBox['yMax']));
                break;
        }
    }

    /**
     * @return mixed
     */
    public function getUnderlineThickness()
    {
        return $this->underlineThickness;
    }

    /**
     * @return mixed
     */
    public function getUnderlinePosition()
    {
        return $this->underlinePosition;
    }
}