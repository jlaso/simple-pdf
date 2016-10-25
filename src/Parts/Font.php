<?php

namespace PHPfriends\SimplePdf\Parts;

use PHPfriends\SimplePdf\Exceptions\KeyNotAllowedOnDictException;
use PHPfriends\SimplePdf\Exceptions\KeyRequiredOnDictException;
use PHPfriends\SimplePdf\Exceptions\ValueNotValidOnDictException;

class Font extends Dictionary
{
    use LazyReferenceTrait;

    const TYPE = 'Font';

    const TYPE1 = 'Type1';
    const TRUETYPE = 'TrueType';

    // Type1 (internal) fonts
    const HELVETICA = 'Helvetica';
    /**
    • Times-Roman
    • Times-Bold
    • Times-Italic
    • Times-BoldItalic
    • Helvetica
    • Helvetica-Bold
    • Helvetica-Oblique
    • Helvetica-BoldOblique
    • Courier
    • Courier-Bold
    • Courier-Oblique
    • Courier-BoldOblique
    • Symbol
    • ZapfDingbats
     */

    /** @var string */
    protected $name;

    protected $allowed = [
        'Type' => [
            'required' => true,
            'options' => [ 'Font' ],
        ],
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
        'FirstChar' => [
            'required' => false,
        ],
        'LastChar' => [
            'required' => false,
        ],
        'Widths' => [
            'required' => false,
        ],
        'FontDescriptor' => [
            'required' => false,
        ],
        'Encoding' => [
            'required' => false,
            'options' => [ 'MacRomanEncoding' ],
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

        parent::addItem('Subtype', new PdfName($subType));
        parent::addItem('BaseFont', new PdfName($baseFont));
        parent::addItem('Name', new PdfName($this->name));

        parent::__construct();
    }

    /**
     * @param string $name
     * @param PartInterface $item
     * @return mixed
     * @throws KeyNotAllowedOnDictException
     */
    public function addItem($name, PartInterface $item)
    {
        if(!isset($this->allowed[$name])){
            throw new KeyNotAllowedOnDictException("Key `{$name}` is not allowed on Font dictionary");
        }

        return parent::addItem($name, $item);
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
     * @throws KeyRequiredOnDictException
     * @throws ValueNotValidOnDictException
     */
    public function dump()
    {
        foreach($this->allowed as $allowedField => $allowedInfo){
            if($allowedInfo['required'] && !isset($this->data[$allowedField])){
                throw new KeyRequiredOnDictException("Key `{$allowedField}`is required in Font dict`");
            }
            if(isset($this->data[$allowedField]) &&
                isset($allowedInfo['options']) &&
                !in_array($this->data[$allowedField], $allowedInfo['options']))
            {
                throw new ValueNotValidOnDictException('Value `'.$this->data[$allowedField].'` not in the options allowed ['.join(',',$allowedInfo['options']).']');
            }
        }

        return parent::dump();
    }

}