<?php

namespace PHPfriends\SimplePdf\Main;

class MarkdownFlow
{
    const NONE = 'none';
    const IN_PARAGRAPH = 'paragraph';
    const IN_HEADER = 'header';
    const IN_TABLE = 'table';
    const IN_STRONG = 'strong';
    const IN_EMPHASIS = 'emphasis';
    const IN_CODE = 'code';
    const IN_IMAGE = 'image';
    const END = 'end';

    protected $status = self::NONE;
    protected $info = null;
    protected $matchedText = '';

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    private function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getMatchedText()
    {
        return $this->matchedText;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function ahead($text)
    {
        if (empty($text)) {
            $this->setStatus(self::END);

            return $text;
        }
        //if(self::IN_HEADER == $this->getStatus()){
            $this->setStatus(self::NONE);
        //}

        switch ($this->status) {
            case self::NONE:
                if ($info = $this->matchHeaderMark($text)) {
                    $this->setStatus(self::IN_HEADER);
                    $this->info = $info;
                    break;
                }
                if ($this->matchParagraph($text)) {
                    $this->setStatus(self::IN_PARAGRAPH);
                    $this->info = true;
                    break;
                }
                $this->setStatus(self::NONE);
                break;

            case self::IN_HEADER:
                break;

            case self::IN_PARAGRAPH:
                if ($info = $this->matchEmphasis($text)) {
                    $this->setStatus(self::IN_EMPHASIS);
                    $this->info = $info;
                    break;
                }
                if ($info = $this->matchStrong($text)) {
                    $this->setStatus(self::IN_STRONG);
                    $this->info = $info;
                    break;
                }
                break;
        }

        return $text;
    }

    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param string $text
     *
     * @return bool
     */
    private function matchEmphasis(&$text)
    {
        if (preg_match('/^(\*|_)(?<text>.*?)\1/', $text, $match)) {
            $this->matchedText = $match['text'];
            $text = preg_replace('/^(\*|_)(.*?)\1/', '\2', $text);

            return true;
        }
        $this->matchedText = '';

        return false;
    }

    /**
     * @param string $text
     *
     * @return bool
     */
    private function matchStrong(&$text)
    {
        if (preg_match('/^(\*\*|__)(?<text>.*?)\1/', $text, $match)) {
            $this->matchedText = $match['text'];
            $text = preg_replace('/^(\*\*|__)(.*?)\1/', '\2', $text);

            return true;
        }
        $this->matchedText = '';

        return false;
    }

    /**
     * @param string $text
     *
     * @return bool
     */
    private function matchParagraph(&$text)
    {
        echo "-----{$text}-----\n\n";
        if (preg_match("/(?<tot>(^|\n\n)(?<text>.*?)(\n$|$|\n\n))/s", $text, $match)) {
            $this->matchedText = str_replace("\n", ' ', $match['text']);
            echo '>>>>>'.$this->matchedText."<<<<<\n\n";
            $text = substr($text, strlen($match['tot']));

            return true;
        }
        $this->matchedText = '';

        return false;
    }

    /**
     * @param $text
     *
     * @return int
     */
    private function matchHeaderMark(&$text)
    {
        if (preg_match('/^(?<tot>(?<h>#{1,6})\s*(?<text>.*?)\s*#*($|\n))/', $text, $match)) {
            $this->matchedText = $match['text'];
            $text = ltrim(substr($text, strlen($match['tot'])));

            return strlen($match['h']);
        }

        if (preg_match("/^(?<text>.*)\n=+(\n|$)/", $text, $match)) {
            $this->matchedText = $match['text'];
            $text = preg_replace("/(^.*)\n=+(\n|$)/", '\1', $text);

            return 1;
        }

        if (preg_match("/^(?<text>.*)\n-+(\n|$)/", $text, $match)) {
            $this->matchedText = $match['text'];
            $text = preg_replace("/(^.*)\n-+(\n|$)/", '\1', $text);

            return 2;
        }
        $this->matchedText = '';

        return 0;
    }
}
