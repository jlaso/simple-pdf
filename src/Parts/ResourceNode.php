<?php

namespace JLaso\SimplePdf\Parts;

class ResourceNode extends Dictionary
{
    use LazyReferenceTrait;

    /** @var Dictionary */
    protected $fontDict;

    public function __construct()
    {
        parent::__construct();

        $this->fontDict = new Dictionary();
    }


    /**
     * @param Font $font
     */
    public function addFont(Font $font)
    {
        $this->fontDict->addItem($font->getName(), $font);
    }

    /**
     * @return string
     */
    public function dump()
    {
        $this->addItem('Font', $this->fontDict);

        return parent::dump();
    }
}