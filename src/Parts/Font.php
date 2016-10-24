<?php

namespace PHPfriends\SimplePdf\Parts;

class Font extends Dictionary
{
    use LazyReferenceTrait;

    const TYPE = 'Font';

    const TYPE1 = 'Type1';
    const HELVETICA = 'Helvetica';
    /**
    • Times-Roman
    • Times-Bold
    • Times-Italic
    • Times-BoldItalic • Helvetica
    • Helvetica-Bold
    • Helvetica-Oblique
    • Helvetica-BoldOblique • Courier
    • Courier-Bold
    • Courier-Oblique
    • Courier-BoldOblique • Symbol
    • ZapfDingbats
     */

    /** @var string */
    protected $subType;
    /** @var string */
    protected $baseFont;
    /** @var string */
    protected $name;

    /**
     * @param string $name
     * @param string $subType
     * @param string $baseFont
     */
    public function __construct($name, $subType, $baseFont)
    {
        $this->subType = $subType;
        $this->baseFont = $baseFont;
        $this->name = $name;

        parent::__construct();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function dump()
    {
        $this->addItem('Subtype', new PdfName($this->subType));
        $this->addItem('BaseFont', new PdfName($this->baseFont));
        $this->addItem('Name', new PdfName($this->name));

        return parent::dump();
    }

}