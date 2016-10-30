<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

use PHPfriends\SimplePdf\Exceptions\ValueNotValidException;

class ResourceNode extends Dictionary
{
    use LazyReferenceTrait;

    /** @var Dictionary */
    protected $fontDict;
    /** @var PdfArray */
    protected $procSet;

    public function __construct()
    {
        $this->fontDict = new Dictionary();
        $this->procSet = new PdfArray();
    }

    /**
     * @param FontDict $font
     */
    public function addFont(FontDict $font)
    {
        $this->fontDict->addItem($font->getAlias(), $font);
    }

    /**
     * options
     * -------
     * PDF      Painting and graphics state
     * Text     Text
     * ImageB   Grayscale images or image masks
     * ImageC   Color images
     * ImageI   Indexed (color-table) images
     *
     * @param PartInterface|string $procSet
     * @throws ValueNotValidException
     */
    public function addProcSet($procSet)
    {
        if(is_string($procSet)) {
            $procSet = new PdfName($procSet);
        }
        if(!$procSet instanceof PartInterface){
            throw new ValueNotValidException('ProcSet '.print_r($procSet, true).' not allowed in addProcSet');
        }
        $this->procSet->addItem($procSet);
    }

    /**
     * @return string
     */
    public function dump()
    {
        $this->addItem('ProcSet', $this->procSet);
        $this->addItem('Font', $this->fontDict);

        return parent::dump();
    }
}