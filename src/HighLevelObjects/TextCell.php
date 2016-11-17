<?php

namespace PHPfriends\SimplePdf\HighLevelObjects;

use PHPfriends\SimplePdf\LowLevelParts\Content;

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
    /** @var float */
    protected $charSpace;
    /** @var float */
    protected $wordSpace;

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
        $this->charSpace = null;
        $this->wordSpace = null;
    }

    /**
     * @param float $charSpace
     */
    public function setCharSpace($charSpace)
    {
        $this->charSpace = $charSpace;
    }

    /**
     * @param float $wordSpace
     */
    public function setWordSpace($wordSpace)
    {
        $this->wordSpace = $wordSpace;
    }

    /**
     * @param array $options
     * @return Content
     */
    public function dump($options = [])
    {
        $content = new Content();
        $this->addToContent($content);

        return $content;
    }

    /**
     * @param Content $content
     */
    public function addToContent(Content &$content)
    {
        $options = [];
        $this->charSpace && $options['char_spacing'] = $this->charSpace;
        $this->wordSpace && $options['word_spacing'] = $this->wordSpace;
        $content->addText($this->x, $this->y, $this->font, $this->fontSize, $this->text, $options);
    }
}