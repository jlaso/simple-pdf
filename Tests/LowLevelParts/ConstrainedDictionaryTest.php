<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

class TestConstrainedDictionary extends ConstrainedDictionary
{
    protected $allowed = [
        'RequiredKey' => [
            'required' => true,
        ],
        'NoRequiredKey' => [
            'required' => false,
        ],
        'ConstrainedKey' => [
            'required' => true,
            'options' => [ 'abc', 'def' ],
        ]
    ];
}

class TestEmptyConstrainedDictionary extends ConstrainedDictionary {}

class ConstrainedDictionaryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \PHPfriends\SimplePdf\Exceptions\KeyNotAllowedOnDictException
     */
    public function testNotAllowedKey()
    {
        $dict = new TestConstrainedDictionary();
        $name = uniqid('A');
        $value = uniqid();
        $dict->addItem($name, new PdfString($value));
    }

    /**
     * @expectedException \PHPfriends\SimplePdf\Exceptions\ValueNotValidOnDictException
     */
    public function testNotAllowedOption()
    {
        $dict = new TestConstrainedDictionary();
        $value = uniqid('ghi');
        $dict->addItem('ConstrainedKey', new PdfString($value));
    }

    /**
     * @expectedException \PHPfriends\SimplePdf\Exceptions\KeyRequiredOnDictException
     */
    public function testRequiredNotPresent()
    {
        $dict = new TestConstrainedDictionary();
        $dict->addItem('NoRequiredKey', new PdfString('testValue'));
        $dict->dump();
    }

    public function testAllWorksFine()
    {
        $dict = new TestConstrainedDictionary();
        $dict->addItem('RequiredKey', new PdfString('testValue'));
        $dict->addItem('ConstrainedKey', new PdfString('abc'));

        $this->assertTrue(strlen($dict->dump()) > 0);
    }

    /**
     * @expectedException \PHPfriends\SimplePdf\Exceptions\NotAllowedDeclaredException
     */
    public function testNotAllowedDeclaredException()
    {
        $dict = new TestEmptyConstrainedDictionary();
    }
}

