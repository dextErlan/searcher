<?php

namespace Searcher\Events;


use Symfony\Component\EventDispatcher\Event;

class OperatorEvent extends Event
{
    const EVENT_NAME = 'searcher.operator';
    /**
     * @var
     */
    private $operator;
    /**
     * @var
     */
    private $field;
    /**
     * @var
     */
    private $value;

    public function __construct($operator,$field,$value)
    {
        $this->operator = $operator;
        $this->field = $field;
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

}