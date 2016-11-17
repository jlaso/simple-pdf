<?php


namespace PHPfriends\SimplePdf\Adaptor;


interface HyphenatorInterface
{
    /**
     * @param string $lang
     * @return void
     */
    public function setLanguage($lang);

    /**
     * @param string $text
     * @return string
     */
    public function hyphenate($text);

    /**
     * @return string
     */
    public function getHyphen();
}