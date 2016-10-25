<?php

namespace PHPfriends\SimplePdf\Parts;

class PdfNameTest extends \PHPUnit_Framework_TestCase
{
    public function testDumpValue()
    {
        $uniq = md5(uniqid());

        $test = new PdfName($uniq);

        $this->assertEquals('/'.$uniq, $test->dump());
    }

    /**
     * demonstrates that a PdfName can be compared in a in_array sentence
     * because its _toString method
     */
    public function testInArrayValue()
    {
        $uniq = md5(uniqid());

        $array = ['abc', 'def', $uniq, 'ghi'];

        $test = new PdfName($uniq);

        $this->assertTrue(in_array($test, $array));
    }
}