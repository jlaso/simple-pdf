<?php

namespace PHPfriends\SimplePdf\Events;

class PageStartedEvent extends BaseEvent
{
    const NAME = 'page.started';

    /** @var int */
    protected $pageOnSection;
    /** @var string */
    protected $pageType;

    /**
     * @param string $pageType
     * @param int $pageOnSection
     */
    public function __construct($pageType, $pageOnSection)
    {
        $this->pageType = $pageType;
        $this->pageOnSection = $pageOnSection;
    }

    /**
     * @return int
     */
    public function getPageOnSection()
    {
        return $this->pageOnSection;
    }

    /**
     * @return string
     */
    public function getPageType()
    {
        return $this->pageType;
    }
}