<?php

namespace PHPfriends\SimplePdf\Parts;

class DictionaryTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyDictionaryValue()
    {
        $dict = new Dictionary();

        $this->assertRegExp("<<\s*>>", $dict->dump());
    }

    public function testStringValue()
    {
        $dict = new Dictionary();
        $name = uniqid('A');
        $value = uniqid();
        $dict->addItem($name, new PdfString($value));

        $this->assertRegExp("<<\s*\/{$name}\s+\({$value}\)\s*>>", $dict->dump());
    }
}