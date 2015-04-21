<?php

namespace Searcher\Events;


use Symfony\Component\EventDispatcher\Event;

class OrderEvent extends Event
{
    const EVENT_NAME = 'searcher.order';
    private $direction;
    private $field;

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