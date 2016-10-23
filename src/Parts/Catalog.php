<?php


namespace JLaso\SimplePdf\Parts;

class Catalog extends Dictionary
{
    const TYPE = 'Catalog';

    use LazyReferenceTrait;
}