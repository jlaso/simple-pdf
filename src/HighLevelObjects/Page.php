<?php

namespace PHPfriends\SimplePdf\HighLevelObjects;

class Page
{
    /** @var int */
    protected $pageNum;
    /** @var ContentInterface[] */
    protected $contents;

    /**
     * @param int $num
     */
    public function __construct($num)
    {
        $this->pageNum = $num;
    }


    /**
     * @param ContentInterface $content
     */
    public function addContent(ContentInterface &$content)
    {
        $this->contents[] = $content;
    }

    /**
     * @return ContentInterface[]
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @return int
     */
    public function getPageNum()
    {
        return $this->pageNum;
    }
}