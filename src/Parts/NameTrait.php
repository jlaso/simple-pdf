<?php

namespace PHPfriends\SimplePdf\Parts;

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