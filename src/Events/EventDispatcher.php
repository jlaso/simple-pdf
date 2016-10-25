<?php

namespace PHPfriends\SimplePdf\Events;

use Symfony\Component\EventDispatcher\EventDispatcher as BaseEventDispatcher;

class EventDispatcher extends BaseEventDispatcher
{
    public function dispatchEvent(BaseEvent $event)
    {
        parent::dispatch($event::NAME, $event);
    }
}