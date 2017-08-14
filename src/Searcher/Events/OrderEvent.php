<?php

namespace Searcher\Events;


use Symfony\Component\EventDispatcher\Event;

class OrderEvent extends Event
{
    /**
     * @deprecated
     * @see EventNames::ORDER_EVENT
     */
    const EVENT_NAME = EventNames::ORDER_EVENT;

    private $direction;
    private $field;

    /**
     * OrderEvent constructor.
     * @param $field
     * @param $direction
     */
    public function __construct($field, $direction)
    {
        $this->direction = $direction;
        $this->field = $field;
    }

    /**
     * @return mixed
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

}