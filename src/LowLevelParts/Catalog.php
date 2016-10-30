<?php


namespace PHPfriends\SimplePdf\LowLevelParts;

class Catalog extends Dictionary
{
    const TYPE = 'Catalog';

    use LazyReferenceTrait;
}