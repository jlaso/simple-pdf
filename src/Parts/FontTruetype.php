<?php

namespace PHPfriends\SimplePdf\Parts;

class FontDictTruetype extends FontDict
{
    protected $allowed = [
        'Type' => [
            'required' => true,
            'options' => [ 'Font' ],
        ],
        'Subtype' => [
            'required' => true,
            'options' => [ self::TRUETYPE ],
        ],
        'BaseFont' => [
            'required' => true,
        ],
        'Name' => [
            'required' => true,
        ],
        'FirstChar' => [
            'required' => true,
        ],
        'LastChar' => [
            'required' => true,
        ],
        'Widths' => [
            'required' => true,
        ],
        'FontDescriptor' => [
            'required' => true,
        ],
        'Encoding' => [
            'required' => false,
            'options' => [ 'MacRomanEncoding' ],
        ]
    ];

    /**
     * @param string $name
     * @param string $baseFont
     */
    public function __construct($name, $baseFont)
    {
        $this->name = $name;

        parent::__construct($name, self::TRUETYPE, $baseFont);
    }

}