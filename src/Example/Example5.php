<?php

namespace PHPfriends\SimplePdf\Example;

include __DIR__.'/../../vendor/autoload.php';

use PHPfriends\SimplePdf\Adaptor\SyllableAdaptor;
use PHPfriends\SimplePdf\Main\HighLevelPdf;
use PHPfriends\SimplePdf\Main\LowLevelPdf;
use PHPfriends\SimplePdf\LowLevelParts\Box;
use PHPfriends\SimplePdf\LowLevelParts\Content;
use PHPfriends\SimplePdf\LowLevelParts\FontDict;
use PHPfriends\SimplePdf\LowLevelParts\PageNode;
use PHPfriends\SimplePdf\LowLevelParts\PagesNode;
use PHPfriends\SimplePdf\LowLevelParts\PdfDate;
use PHPfriends\SimplePdf\LowLevelParts\ResourceNode;

class Example5 extends AbstractExample
{
    protected $pdf;
    protected $verbose = true;

    public function process()
    {
        $pdf = new HighLevelPdf(612.0, 792.0, $this->verbose);
        $pdf->setHyphenator(new SyllableAdaptor());

        $pdf->setMetadata('Title', 'Example 5 @ High Level');
        $pdf->setMetadata('Author', '@PHPfriendsTK');
        $pdf->setMetadata('Creator', 'https://github.com/PHPfriends/simple-pdf');
        $pdf->setMetadata('Producer', 'https://packagist.org/packages/phpfriends/simple-pdf');
        $pdf->setMetadata('CreationDate', new PdfDate());
        $pdf->setMetadata('Keywords', ['simple-pdf','example','PHPfriends']);

        //$pdf->setFont('FreeUniversal', 'Regular', 24);
        $pdf->setFont('Lato', 'Regular', 24);
        $pdf->setCell();
        $text = <<<EOD
This is a long text. The idea is trying to use all the room possible in a single row and get the next row justified! do you thing that will going to be possible ? Let\'s try and will see.
EOD;
        $pdf->writeTextJustify($text, 'en-us');

        $pdf->rectangle();

        $pdf->saveToFile(__DIR__.'/test5.pdf');
    }
}

Example5::main();
