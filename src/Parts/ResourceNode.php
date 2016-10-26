<?php

namespace PHPfriends\SimplePdf\Parts;

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
     * @param FontDict $font
     */
    public function addFont(FontDict $font)
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