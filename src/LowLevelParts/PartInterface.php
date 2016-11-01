<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

interface PartInterface
{
    public function dump();

    public function __toString();
}