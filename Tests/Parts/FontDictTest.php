<?php

namespace PHPfriends\SimplePdf\Parts;

class FontDictTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \PHPfriends\SimplePdf\Exceptions\ValueNotValidOnDictException
     */
    public function testNotAllowedOption()
    {
        $font = new FontDict(FontDict::HELVETICA, FontDict::TYPE1,FontDict::HELVETICA);

        $font->addItem('Encoding', new PdfName('ImpossibleEncoding'));
    }
}