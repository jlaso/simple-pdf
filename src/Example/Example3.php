<?php

namespace PHPfriends\SimplePdf\Example;

include __DIR__.'/../../vendor/autoload.php';

/**
 * Testing hyphenation
 */
class Example3 extends AbstractExample
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
}

Example3::main();