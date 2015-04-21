<?php

namespace Searcher\Events;


use Symfony\Component\EventDispatcher\Event;

class GroupEvent extends Event
{
    const EVENT_NAME = 'searcher.group';
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