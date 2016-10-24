<?php

namespace PHPfriends\SimplePdf\Example;

include __DIR__.'/../../vendor/autoload.php';

use PHPfriends\SimplePdf\Main\HighLevelPdf;
use PHPfriends\SimplePdf\Main\LowLevelPdf;
use PHPfriends\SimplePdf\Parts\Box;
use PHPfriends\SimplePdf\Parts\Content;
use PHPfriends\SimplePdf\Parts\Font;
use PHPfriends\SimplePdf\Parts\PageNode;
use PHPfriends\SimplePdf\Parts\PagesNode;
use PHPfriends\SimplePdf\Parts\ResourceNode;

class Example4
{
    protected $pdf;

    public function process()
    {
        $pdf = new HighLevelPdf();

        $pdf->setMetadata('Author', 'JLaso');
        $pdf->setMetadata('Creator', 'https://github.com/PHPfriends/simple-pdf');

        $pdf->setFont('Helvetica', null, 48);
        $pdf->setCell();
        $pdf->writeTextJustify('Hello world !');

        // WIP

        $pdf->saveToFile(__DIR__.'/test.pdf');
    }

    public static function main()
    {
        $o = new Example1();
        $o->process();
    }
}

Example1::main();
echo "Done! \n";