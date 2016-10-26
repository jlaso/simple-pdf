<?php

namespace PHPfriends\SimplePdf\Parts;

class FontDescriptorDict extends ConstrainedDictionary
{
    use LazyReferenceTrait;

    const TYPE = 'FontDescriptor';

    protected $allowed = [
        'FontName' => [
            'required' => true,
        ],
        'FontFamily' => [
            'required' => false,
        ],
        'FontStretch' => [
            'required' => false,
        ],
        'FontWeight' => [
            'required' => false,
        ],
        'Flags' => [  // page 459 pdf_reference_1.7
            'required' => true,
        ],
        'FontBBox' => [
            'required' => true,
        ],
        'ItalicAngle' => [
            'required' => true,
        ],
        'Ascent' => [
            'required' => true,
        ],
        'Descent' => [
            'required' => true,
        ],
        'Leading' => [
            'required' => false,
        ],
        'CapHeight' => [
            'required' => true,
        ],
        'XHeight' => [
            'required' => false,
        ],
        'StemV' => [
            'required' => true,
        ],
        'StemH' => [
            'required' => false,
        ],
        'AvgWidth' => [
            'required' => false,
        ],
        'MaxWidth' => [
            'required' => false,
        ],
        'MissingWidth' => [
            'required' => false,
        ],
        'FontFile' => [
            'required' => false,
        ],
        'FontFile2' => [
            'required' => false,
        ],
        'FontFile3' => [
            'required' => false,
        ],
        'CharSet' => [
            'required' => false,
        ]
    ];

    /** @var string */
    protected $fontFile;
    /** @var FontFileStream */
    protected $fontFileStream;

    /**
     * @param $fontFile
     */
    public function __construct($fontFile)
    {
        parent::__construct();
        $this->fontFile = $fontFile;
        $this->fontFileStream = new FontFileStream($this->fontFile);
        $this->addItem('FontFile2', $this->fontFileStream);
    }

    /**
     * @param string $key
     * @return PartInterface
     * @throws \Exception
     */
    public function getItem($key)
    {
        $key = ltrim($key, "\\");
        if(!isset($this->data[$key])){
            throw new \Exception("Key `{$key}` does not exist in that Font dictionary");
        }

        return $this->data[$key];
    }

    public function getItems()
    {
        return $this->data;
    }

    /**
     * @return FontFileStream
     */
    public function getFontFileStream()
    {
        return $this->fontFileStream;
    }

}