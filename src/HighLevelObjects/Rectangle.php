<?php

namespace PHPfriends\SimplePdf\HighLevelObjects;

use PHPfriends\SimplePdf\LowLevelParts\Content;

class Rectangle implements ContentInterface
{
    /** @var float */
    protected $x;
    /** @var float */
    protected $y;
    /** @var float */
    protected $w;
    /** @var float */
    protected $h;
    /** @var string */
    protected $color;
    /** @var int */
    protected $stroke;

    /**
     * @param float  $x
     * @param float  $y
     * @param float  $w
     * @param float  $h
     * @param string $color
     * @param int    $stroke
     */
    public function __construct($x, $y, $w, $h, $color = '0 0 0', $stroke = 4)
    {
        $this->x = $x;
        $this->y = $y;
        $this->w = $w;
        $this->h = $h;
        $this->color = $color;
        $this->stroke = $stroke;
    }

    /**
     * @param array $options
     *
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
        $content->addRectangle($this->x, $this->y, $this->w, $this->h, $this->color, $this->stroke);
    }
}
