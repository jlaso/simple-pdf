<?php

namespace PHPfriends\SimplePdf\Example;

include __DIR__.'/../../vendor/autoload.php';

use PHPfriends\SimplePdf\Main\HighLevelPdf;
use PHPfriends\SimplePdf\Main\LowLevelPdf;
use PHPfriends\SimplePdf\Parts\Box;
use PHPfriends\SimplePdf\Parts\Content;
use PHPfriends\SimplePdf\Parts\FontDict;
use PHPfriends\SimplePdf\Parts\PageNode;
use PHPfriends\SimplePdf\Parts\PagesNode;
use PHPfriends\SimplePdf\Parts\ResourceNode;

class Example4
{
    protected $pdf;

    public function process()
    {
        $pdf = new HighLevelPdf(612.0, 792.0);

        $pdf->setMetadata('Author', '@PHPfriendsTK');
        $pdf->setMetadata('Creator', 'https://github.com/PHPfriends/simple-pdf');

        $pdf->setFont('Neuton', 'Regular', 48);
        $pdf->setCell();
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