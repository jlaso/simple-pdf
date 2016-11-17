<?php

namespace PHPfriends\SimplePdf\Adaptor;

use PHPfriends\SimplePdf\Common\Config;

class SyllableAdaptor implements HyphenatorInterface
{
    protected $syllable;
    protected $hyphen;

    public function __construct()
    {
        $config = Config::getInstance();
        $this->hyphen = $config->get('hyphenation.hyphen');
        $this->syllable = new \Syllable($config->get('hyphenation.language'));
        // Set the directory where Syllable can store cache files
        $this->syllable->getCache()->setPath($config->getCacheDir());
    }

    public function setLanguage($lang)
    {
        $this->syllable->setLanguage($lang);
    }

    public function hyphenate($text)
    {
        return str_replace('&shy;', $this->hyphen, $this->syllable->hyphenateText($text));
    }

    public function getHyphen()
    {
        return $this->hyphen;
    }
}
