<?php

namespace Searcher\Events;


use Symfony\Contracts\EventDispatcher\Event;

class GroupEvent extends Event
{
    const EVENT_NAME = EventNames::GROUP_EVENT;
    private $groupName;

    public function __construct($groupName)
    {
        $this->groupName = $groupName;
    }

    /**
     * @return mixed
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

}