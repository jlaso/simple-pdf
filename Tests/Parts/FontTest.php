<?php

namespace PHPfriends\SimplePdf\Parts;

class FontTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @ExceptionExpected \PHPfriends\SimplePdf\Exceptions\ValueNotValidOnDictException
     */
    public function testNotAllowedOption()
    {
        $font = new Font(Font::HELVETICA, Font::TYPE1,Font::HELVETICA);

        $font->addItem('Encoding', new PdfName('ImpossibleEncoding'));
    }
}