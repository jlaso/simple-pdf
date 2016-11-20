<?php

namespace PHPfriends\SimplePdf\Example;

abstract class AbstractExample
{
    abstract function process();

    public static function main()
    {
        $o = new static();
        $o->process();
        echo "Done! \n";
    }
}