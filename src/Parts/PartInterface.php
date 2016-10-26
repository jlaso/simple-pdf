<?php

namespace PHPfriends\SimplePdf\Parts;

interface PartInterface
{
    public function dump();

    public function __toString();
}