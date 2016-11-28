<?php

namespace PHPfriends\SimplePdf\Events;

use PHPfriends\SimplePdf\HighLevelObjects\Page;

class LineBreakEvent extends BaseEvent
{
    const NAME = Events::EVT_LINE_BREAK;

    /** @var Page */
    protected $page;

    /**
     * @param Page $page
     */
    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }
}
