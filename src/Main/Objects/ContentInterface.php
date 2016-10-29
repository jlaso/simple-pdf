<?php

namespace PHPfriends\SimplePdf\Main\Objects;

use PHPfriends\SimplePdf\Parts\Content;

interface ContentInterface
{
    /**
     * @param array $options
     * @return Content
     */
    public function dump($options = []);

    /**
     * @param Content $content
     * @return void
     */
    public function addToContent(Content &$content);

}