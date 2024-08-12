<?php

namespace Searcher\Events;


use Symfony\Contracts\EventDispatcher\Event;

class OperatorEvent extends Event
{
    /**
     * @deprecated
     * @see EventNames::OPERATOR_EVENT
     */
    const EVENT_NAME = EventNames::OPERATOR_EVENT;
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

    /**
     * OperatorEvent constructor.
     * @param $operator
     * @param $field
     * @param $value
     */
    public function __construct(& $operator, & $field, & $value)
    {
        $this->operator = &$operator;
        $this->field = &$field;
        $this->value = &$value;
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

    /**
     * @param mixed $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @param mixed $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

}