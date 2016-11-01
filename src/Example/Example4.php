<?php

namespace PHPfriends\SimplePdf\Example;

include __DIR__.'/../../vendor/autoload.php';

use PHPfriends\SimplePdf\Main\HighLevelPdf;
use PHPfriends\SimplePdf\Main\LowLevelPdf;
use PHPfriends\SimplePdf\LowLevelParts\Box;
use PHPfriends\SimplePdf\LowLevelParts\Content;
use PHPfriends\SimplePdf\LowLevelParts\FontDict;
use PHPfriends\SimplePdf\LowLevelParts\PageNode;
use PHPfriends\SimplePdf\LowLevelParts\PagesNode;
use PHPfriends\SimplePdf\LowLevelParts\PdfDate;
use PHPfriends\SimplePdf\LowLevelParts\ResourceNode;

class Example4
{
    protected $pdf;
    protected $verbose = true;

    public function process()
    {
        $pdf = new HighLevelPdf(612.0, 792.0, $this->verbose);

        $pdf->setMetadata('Title', 'Example 4 @ High Level');
        $pdf->setMetadata('Author', '@PHPfriendsTK');
        $pdf->setMetadata('Creator', 'https://github.com/PHPfriends/simple-pdf');
        $pdf->setMetadata('Producer', 'https://packagist.org/packages/phpfriends/simple-pdf');
        $pdf->setMetadata('CreationDate', new PdfDate());
        $pdf->setMetadata('Keywords', ['simple-pdf','example','PHPfriends']);

        $pdf->setFont('Ah_Natural', 'Regular', 48);
        $pdf->setCell();
        $pdf->writeTextJustify('Hello world !');

        $pdf->setFont('Oh_Script', 'Regular', 56);
        $pdf->setCell(null, 100);
        $pdf->writeTextJustify('Hello world !');


        $pdf->saveToFile(__DIR__.'/test4.pdf');
    }

    public static function main()
    {
        $o = new Example4();
        $o->process();
    }
}

Example4::main();
echo "Done! \n";