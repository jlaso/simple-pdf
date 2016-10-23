<?php

namespace JLaso\SimplePdf\Parts;

class Box implements PartInterface
{
    /** @var int */
    protected $x;
    /** @var int */
    protected $y;
    /** @var int */
    protected $w;
    /** @var int */
    protected $h;

    /**
     * @param int $x
     * @param int $y
     * @param int $w
     * @param int $h
     */
    public function __construct($x, $y, $w, $h)
    {
        $this->x = $x;
        $this->y = $y;
        $this->w = $w;
        $this->h = $h;
    }

    /**
     * @return string
     */
    public function dump()
    {
        $array = new PdfArray();
        $array->addItem(new PdfInteger($this->x));
        $array->addItem(new PdfInteger($this->y));
        $array->addItem(new PdfInteger($this->w));
        $array->addItem(new PdfInteger($this->h));

        return $array->dump();
    }
}