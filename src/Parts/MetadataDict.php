<?php


namespace PHPfriends\SimplePdf\Parts;


class MetadataDict extends Dictionary
{
    const NAME = 'Info';

    use NameTrait;
    use LazyReferenceTrait;
}