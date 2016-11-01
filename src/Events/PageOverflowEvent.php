<?php

namespace PHPfriends\SimplePdf\Events;

class PageOverflowEvent extends BaseEvent
{
    const NAME = 'page.overflow';

    /** @var int */
    protected $pageNum;

    /**
     * @param int $pageNum
     */
    public function __construct($pageNum)
    {
        $this->pageNum = $pageNum;
    }

    /**
     * @return int
     */
    public function getPageNum()
    {
        return $this->pageNum;
    }

    /**
     * @param int $pageNum
     */
    public function setPageNum($pageNum)
    {
        $this->pageNum = $pageNum;
    }
}