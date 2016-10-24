<?php

namespace PHPfriends\SimplePdf\Parts;

class PdfNumberTest extends \PHPUnit_Framework_TestCase
{
    public function testDumpValue()
    {
        $random = rand(0,PHP_INT_MAX);

        $pdfNumber = new PdfNumber($random);

        $this->assertEquals($random, $pdfNumber->dump());
    }
}