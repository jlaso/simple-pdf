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

        $pdf->setFont('Lato', 'Regular', 20);
        $pdf->setCell();
        $text = <<<EOD
This is a long text. The idea is trying to use all the room possible in a single row and get the next row justified! do you thing that will going to be possible? Let\'s try and will see.
Another long line but this time without estrange or special quantity of adjectives or longitude or latitude points of view.
What is Lorem Ipsum?
Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
EOD;
        $pdf->writeTextJustify($text, 'en-us');

        $pdf->rectangle();

        $pdf->saveToFile(__DIR__.'/test5.pdf');
    }
}

Example5::main();
