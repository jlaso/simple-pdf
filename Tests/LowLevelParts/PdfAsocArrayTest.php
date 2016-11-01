<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

class PdfAsocArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testDumpValue()
    {
        $array = [
            'one' => 1,
            'two' => 2,
            'three' => 3,
            'four' => 4,
            'five' => 5,
        ];

        // create the PdfAssocArray with the probe array
        $pdfArray = new PdfAssocArray();
        foreach($array as $key => $value){
            $pdfArray->addItem($key, new PdfNumber($value));
        }

        $result = $pdfArray->dump();

        // check that is an Array, is wrapper by brackets
        $this->assertEquals('[', $result[0]);
        $this->assertEquals(']', substr($result, -1));

        // test that the AssocArray in Pdf has the format `(key) value` for every item
        foreach($array as $key => $value){
            $this->assertRegExp("/\s\({$key}\)\s+{$value}\s/", $result);
        }
    }
}