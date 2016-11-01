<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

class PdfReferenceTest extends \PHPUnit_Framework_TestCase
{
    public function testDumpValue()
    {
        $id = rand(1, PHP_INT_MAX);

        $reference = new Reference($id);

        $this->assertEquals("{$id} 0 R", $reference->dump());
    }
}