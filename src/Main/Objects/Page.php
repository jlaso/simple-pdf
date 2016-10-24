<?php

namespace PHPfriends\SimplePdf\Main\Objects;

class Page
{
    protected $contents;

    public function addContent($content)
    {
        $this->contents[] = $content;
    }

    /**
     * @return mixed
     */
    public function getContents()
    {
        return $this->contents;
    }

}