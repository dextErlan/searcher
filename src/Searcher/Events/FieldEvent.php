<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 17.04.15
 * Time: 14:53
 */

namespace Searcher\Events;


use Symfony\Component\EventDispatcher\Event;

class FieldEvent extends Event
{
    const EVENT_NAME = 'searcher.field';
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