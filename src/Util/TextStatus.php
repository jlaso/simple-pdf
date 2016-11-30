<?php

namespace PHPfriends\SimplePdf\Util;

use PHPfriends\SimplePdf\HighLevelObjects\Font;

class TextStatus
{
    /** @var Font */
    public $font;
    /** @var array */
    public $widths;
    /** @var int */
    public $status;
    /** @var float */
    public $size;
    /** @var float */
    public $hOffset;

    /**
     * @param Font $font
     * @param array $widths
     * @param float $size
     * @param float $hOffset
     */
    public function __construct(Font $font, array $widths, $size, $hOffset = 0.0)
    {
        $this->font = $font;
        $this->widths = $widths;
        $this->status = null;
        $this->size = $size;
        $this->hOffset = $hOffset;
    }


}