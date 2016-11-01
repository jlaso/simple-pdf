<?php

namespace PHPfriends\SimplePdf\Example;

use PHPfriends\SimplePdf\Measurement\FontMetrics;

include __DIR__.'/../../vendor/autoload.php';

/**
 * Determine the widths of the chars in a specific Font
 */
class Example2 extends AbstractExample
{
    public function process()
    {
        $fontTool = new FontMetrics(FontMetrics::NEUTON, FontMetrics::REGULAR);

        $widths = $fontTool->getWidths();

        print_r($widths);
    }
}

Example2::main();
