<?php

namespace PHPfriends\SimplePdf\Example;

use PHPfriends\SimplePdf\Adaptor\SyllableAdaptor;

include __DIR__.'/../../vendor/autoload.php';

/**
 * Testing hyphenation
 */
class Example3b extends AbstractExample
{
    public function process()
    {
        $hyphenator = new SyllableAdaptor();

        $hyphenator->setLanguage('en-us');
        echo $hyphenator->hyphenate('Provide a plethora of paragraphs')."\r\n";

        $hyphenator->setLanguage('es');
        echo $hyphenator->hyphenate('Este año va a hacer más calor que el año pasado')."\r\n";
    }
}

Example3b::main();