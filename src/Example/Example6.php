<?php

namespace PHPfriends\SimplePdf\Example;

include __DIR__.'/../../vendor/autoload.php';

use PHPfriends\SimplePdf\Adaptor\SyllableAdaptor;
use PHPfriends\SimplePdf\Common\Config;
use PHPfriends\SimplePdf\Events\Events;
use PHPfriends\SimplePdf\Events\LineBreakEvent;
use PHPfriends\SimplePdf\Events\PageBreakEvent;
use PHPfriends\SimplePdf\LowLevelParts\PdfDate;
use PHPfriends\SimplePdf\Main\MarkdownPdf;

class Example6 extends AbstractExample
{
    protected $pdf;
    protected $verbose = true;
    protected $lines = 0;
    protected $pages = 1;

    public function process()
    {
        $config = Config::getInstance()->get('styles');
        $pdf = new MarkdownPdf($config, $this->verbose);
        $pdf->setHyphenator(new SyllableAdaptor());
        $pdf->getEventDispatcher()->addListener(Events::EVT_LINE_BREAK, [$this, 'lineBreakListener']);
        $pdf->getEventDispatcher()->addListener(Events::EVT_PAGE_BREAK, [$this, 'pageBreakListener']);

        $pdf->setMetadata('Title', 'Example 6 @ Markdown');
        $pdf->setMetadata('Author', '@PHPfriendsTK');
        $pdf->setMetadata('Creator', 'https://github.com/PHPfriends/simple-pdf');
        $pdf->setMetadata('Producer', 'https://packagist.org/packages/phpfriends/simple-pdf');
        $pdf->setMetadata('CreationDate', new PdfDate());
        $pdf->setMetadata('Keywords', ['simple-pdf', 'example', 'PHPfriends']);

        $text = <<<'EOD'
# Chapter 1

## Subchapter

### Section

Once upon a time ... 

This is a _real_ example of the *first* try to **interpret** and print markdown as
pdf. The next things will consist in improve the language the system is able
to recognize.

EOD;

        $pdf->setCell();
        $pdf->mdWriteTextJustify($text, 'en-us');

        $pdf->rectangle();

        $pdf->saveToFile(__DIR__.'/test6.pdf');

        echo sprintf("There were %d lines printed\n", $this->lines);
        echo sprintf("And %d pages printed\n", $this->pages);
    }

    public function lineBreakListener(LineBreakEvent $event)
    {
        ++$this->lines;
    }

    public function pageBreakListener(PageBreakEvent $event)
    {
        ++$this->pages;
    }
}

Example6::main();
