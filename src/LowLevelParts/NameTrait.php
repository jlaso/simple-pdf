<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

trait NameTrait
{
    /**
     * @return string
     */
    public function getNameForNamesCatalog()
    {
        return static::NAME;
    }
}