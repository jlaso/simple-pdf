<?php


namespace PHPfriends\SimplePdf\LowLevelParts;


class MetadataDict extends Dictionary
{
    const NAME = 'Info';

    use NameTrait;
    use LazyReferenceTrait;
}