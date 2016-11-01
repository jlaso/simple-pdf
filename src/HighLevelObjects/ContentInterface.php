<?php

namespace PHPfriends\SimplePdf\HighLevelObjects;

use PHPfriends\SimplePdf\LowLevelParts\Content;

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