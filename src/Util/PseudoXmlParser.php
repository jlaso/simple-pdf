<?php

namespace PHPfriends\SimplePdf\Util;

use PHPfriends\SimplePdf\HighLevelObjects\Font;

class PseudoXmlParser
{
    const NONE = 0;
    const BOLD = 1;
    const ITALIC = 2;
    const SUPERSCRIPT = 3;
    const SUBSCRIPT = 4;
    const LINK = 5;

    /** @var Stack */
    protected $stack;
    /** @var TextStatus[] */
    protected $fontSet;
    /** @var int */
    protected $status;

    /**
     * @param TextStatus[] $fontSet
     */
    public function __construct(array $fontSet)
    {
        $this->fontSet = $fontSet;
        $this->stack = new Stack();
        $this->status = self::NONE;
    }

    /**
     * @return int
     */
    public function getNextStatus()
    {
        return $this->stack->top();
    }

    /**
     * @return int
     */
    public function getCurrentStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return int
     */
    private function pushAndGetStatus($status)
    {
        $this->stack->push($status);

        return $status;
    }

    /**
     * @return int
     */
    private function popAndGetStatus()
    {
        return $this->stack->pop();
    }

    /**
     * @param string $text
     * @return int
     */
    public function parse(&$text)
    {
        $status = null;
        $match = null;

        switch (true) {

            case preg_match("/^(?<tag>\<a\>)/", $text, $match):
                $status = self::LINK;
                break;

            case preg_match("/^(?<tag>\<i\>)/", $text, $match):
                $status = self::ITALIC;
                break;

            case preg_match("/^(?<tag>\<b\>)/", $text, $match):
                $status = self::BOLD;
                break;

            case preg_match("/^(?<tag>\<sub\>)/", $text, $match):
                $status = self::SUBSCRIPT;
                break;

            case preg_match("/^(?<tag>\<sup\>)/", $text, $match):
                $status = self::SUPERSCRIPT;
                break;

            case preg_match("/^(?<tag>\<\/>)/", $text, $match):
                $status = -1;
                break;

        }

        if ($match !== null) {
            $text = substr($text, strlen($match['tag']));

            return ($status == -1) ?
                $this->popAndGetStatus() :
                $this->pushAndGetStatus($status);
        }

        return -1;
    }

    /**
     * @param Font $font
     * @param float $size
     * @param array $widths
     */
    public function switchStatus(&$font, &$size, &$widths)
    {
        $status = $this->fontSet[$this->status];
        $font = $status->font;
        $size = $status->size;
        $widths = $status->widths;
    }
}
