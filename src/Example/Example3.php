<?php

namespace PHPfriends\SimplePdf\Example;

include __DIR__.'/../../vendor/autoload.php';

/**
 * Testing hyphenation
 */
class Example3
{
    public function process()
    {
        $syllable = new \Syllable('en-us');

        // Set the directory where Syllable can store cache files
        $syllable->getCache()->setPath(__DIR__ . '/../../cache');

        echo $syllable->hyphenateText('Provide a plethora of paragraphs')."\r\n";

        $syllable->setLanguage('es');
        echo $syllable->hyphenateText('Este año va a hacer más calor que el año pasado')."\r\n";
    }

    public static function main()
    {
        $o = new Example3();
        $o->process();
    }
}

Example3::main();
echo "Done! \n";