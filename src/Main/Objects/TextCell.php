<?php

namespace PHPfriends\SimplePdf\Main\Objects;

use PHPfriends\SimplePdf\Parts\Content;

class TextCell implements ContentInterface
{
    /** @var float */
    protected $x;
    /** @var float */
    protected $y;
    /** @var float */
    protected $w;
    /** @var float */
    protected $h;
    /** @var Font */
    protected $font;
    /** @var float */
    protected $fontSize;

    /** @var string */
    protected $text;

    /**
     * @param float $x
     * @param float $y
     * @param float $w
     * @param float $h
     * @param Font $font
     * @param float $fontSize
     * @param string $text
     */
    public function __construct($x, $y, $w, $h, $font, $fontSize, $text)
    {
        $this->x = $x;
        $this->y = $y;
        $this->w = $w;
        $this->h = $h;
        $this->font = $font;
        $this->fontSize = $fontSize;
        $this->text = $text;
    }

    /**
     * @param array $options
     * @return Content
     */
    public function dump($options = [])
    {
        $content = new Content();
        $content->addText($this->x, $this->y, $this->font->getFontName(), $this->fontSize, $this->text);

        return $content;
    }

}