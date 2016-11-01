<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

use PHPfriends\SimplePdf\Adaptor\FontManager;
use PHPfriends\SimplePdf\Common\GetAliasInterface;

class FontDict extends ConstrainedDictionary implements GetAliasInterface
{
    use LazyReferenceTrait;

    const TYPE = 'Font';

    const TYPE1 = 'Type1';
    const TRUETYPE = 'TrueType';

    // Type1 (internal) fonts
    const TIMES_ROMAN = 'Times-Roman';
    const TIMES_BOLD = 'Times-Bold';
    const TIMES_ITALIC = 'Times-Italic';
    const TIMES_BOLD_ITALIC = 'Times-BoldItalic';
    const HELVETICA = 'Helvetica';
    const HELVETICA_BOLD = 'Helvetica-Bold';
    const HELVETICA_OBLIQUE ='Helvetica-Oblique';
    const HELVETICA_BOLD_OBLIQUE = 'Helvetica-BoldOblique';
    const COURIER = 'Courier';
    const COURIER_BOLD = 'Courier-Bold';
    const COURIER_OBLIQUE = 'Courier-Oblique';
    const COURIER_BOLD_OBLIQUE = 'Courier-BoldOblique';
    const SYMBOL = 'Symbol';
    const ZAPDINGBATS = 'ZapfDingbats';

    /** @var string */
    protected $name;
    /** @var string */
    protected $baseFont;

    protected $allowed = [
        'Subtype' => [
            'required' => true,
            'options' => [ self::TYPE1, self::TRUETYPE ],
        ],
        'BaseFont' => [
            'required' => true,
        ],
        'Name' => [
            'required' => true,
        ],
        'Encoding' => [
            'required' => false,
            // @TODO: Can be a predefined Encoding or a declared one
            'options' => [ '*', 'MacRomanEncoding', 'MacExpertEncoding', 'WinAnsiEncoding' ],
        ]
    ];

    /**
     * @param string $name
     * @param string $subType
     * @param string $baseFont
     */
    public function __construct($name, $subType, $baseFont)
    {
        $this->name = $name;
        $this->baseFont = $baseFont;

        parent::__construct();

        parent::addItem('Subtype', new PdfName($subType));
        parent::addItem('BaseFont', new PdfName($baseFont));
        parent::addItem('Name', new PdfName($this->name));
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
    public function getAlias()
    {
        return FontManager::getInstance()->addFontName($this->baseFont);
    }

}