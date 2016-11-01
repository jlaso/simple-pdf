<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

class PdfArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testDumpValue()
    {
        $array = [ 1,2,3,4,5 ];

        $pdfArray = new PdfArray();
        foreach($array as $item){
            $pdfArray->addItem(new PdfNumber($item));
        }

        $this->assertEquals('[ '.join(' ', $array).' ]', $pdfArray->dump());
    }
}