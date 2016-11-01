<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

class PdfStringTest extends \PHPUnit_Framework_TestCase
{
    public function testDumpValue()
    {
        $uniq = md5(uniqid());

        $pdfString = new PdfString($uniq);

        $this->assertEquals('('.$uniq.')', $pdfString->dump());
    }
}