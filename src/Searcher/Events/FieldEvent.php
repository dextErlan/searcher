<?php

namespace Searcher\Events;


use Symfony\Component\EventDispatcher\Event;

class FieldEvent extends Event
{
    /**
     * @deprecated
     * @see EventNames::FIELD_EVENT
     */
    const EVENT_NAME = EventNames::FIELD_EVENT;

    /**
     * @var
     */
    private $field;

    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

}