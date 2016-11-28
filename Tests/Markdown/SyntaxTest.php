<?php

namespace PHPfriends\SimplePdf\Common;

use PHPfriends\SimplePdf\Main\MarkdownFlow;
use PHPfriends\SimplePdf\Tests\AbstractBaseTestCase;

class SyntaxTest extends AbstractBaseTestCase
{
    /** @var MarkdownFlow */
    protected $mdFlow;

    public function testEmphasis()
    {
        $this->mdFlow = new MarkdownFlow();
        $method = 'matchEmphasis';

        $this->caseForTest($method, 'sample text', '', false);
        $this->caseForTest($method, '_sample text_', 'sample text', true);
        $this->caseForTest($method, '*sample text_', '', false);
        $this->caseForTest($method, '_sample text*', '', false);
        $this->caseForTest($method, '*sample text*', 'sample text', true);
    }

    public function testStrong()
    {
        $this->mdFlow = new MarkdownFlow();
        $method = 'matchStrong';

        $this->caseForTest($method, 'sample text', '', false);
        $this->caseForTest($method, '__sample text__', 'sample text', true);
        $this->caseForTest($method, '_sample text_', '', false);
        $this->caseForTest($method, '*sample text*', '', false);
        $this->caseForTest($method, '**sample text**', 'sample text', true);
    }

    public function testHeader()
    {
        $this->mdFlow = new MarkdownFlow();
        $method = 'matchHeaderMark';

        $this->caseForTest($method, 'not a header', '', 0);
        $this->caseForTest($method, '# header', 'header', 1);
        $this->caseForTest($method, '# header #', 'header', 1);
        $this->caseForTest($method, '# header ##', 'header', 1);
        $this->caseForTest($method, '## header', 'header', 2);
        $this->caseForTest($method, '## header #', 'header', 2);
        $this->caseForTest($method, '## header ##', 'header', 2);
        $this->caseForTest($method, '### header', 'header', 3);
        $this->caseForTest($method, '#### header', 'header', 4);
        $this->caseForTest($method, '##### header', 'header', 5);
        $this->caseForTest($method, '###### header', 'header', 6);
        $this->caseForTest($method, "header\n======\n", 'header', 1);
        $this->caseForTest($method, "header\n======", 'header', 1);
        $this->caseForTest($method, "header\n=\n", 'header', 1);
        $this->caseForTest($method, "header\n=", 'header', 1);
        $this->caseForTest($method, "header\n------\n", 'header', 2);
        $this->caseForTest($method, "header\n------", 'header', 2);
        $this->caseForTest($method, "header\n-\n", 'header', 2);
        $this->caseForTest($method, "header\n-", 'header', 2);
    }

    private function caseForTest($method, $testValue, $expectedValue, $resultValue)
    {
        $text = $testValue;
        $result = $this->invokeMethod($this->mdFlow, $method, [&$text]);
        $this->assertEquals($resultValue, $result);
        $this->assertEquals($expectedValue, $this->mdFlow->getMatchedText());
    }
}
