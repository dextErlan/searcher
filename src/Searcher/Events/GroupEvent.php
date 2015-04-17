<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 17.04.15
 * Time: 14:52
 */

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