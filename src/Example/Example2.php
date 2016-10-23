<?php

namespace JLaso\SimplePdf\Example;

use JLaso\SimplePdf\Measurement\FontMetrics;

include __DIR__.'/../../vendor/autoload.php';

/**
 * Determine the widths of the chars in a specific Font
 */
class Example2
{
    public function process()
    {
        $fontTool = new FontMetrics(FontMetrics::NEUTON, 777, FontMetrics::REGULAR);

        $widths = $fontTool->getWidths();

        print_r($widths);
    }

    public static function main()
    {
        $o = new Example2();
        $o->process();
    }
}

Example2::main();
echo "Done! \n";