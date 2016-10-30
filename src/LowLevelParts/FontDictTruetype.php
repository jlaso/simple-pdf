<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

class FontDictTruetype extends FontDict
{
    protected $allowed = [
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
            'options' => [ 'MacRomanEncoding', 'WinAnsiEncoding' ],
        ]
    ];

    /**
     * @param string $name
     * @param string $baseFont
     */
    public function __construct($name, $baseFont)
    {
        parent::__construct($name, self::TRUETYPE, $baseFont);
    }

}