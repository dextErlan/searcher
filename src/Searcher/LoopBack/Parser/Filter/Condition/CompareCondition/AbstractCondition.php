<?php

namespace Searcher\LoopBack\Parser\Filter\Condition\CompareCondition;


use Searcher\LoopBack\Parser\BuilderInterface;
use Searcher\LoopBack\Parser\Filter\Condition\ConditionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractCondition implements BuilderInterface, ConditionInterface
{

    private $field;
    private $value;
    /**
     * @var EventDispatcherInterface $dispatcher
     */
    protected $dispatcher = null;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     * @return AbstractCondition
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return AbstractCondition
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @static
     * @param $field
     * @param $value
     * @param null $dispatcher
     * @return AbstractCondition
     */
    public static function create($field, $value, $dispatcher = null)
    {
        $instance = new static();
        $instance->setField($field);
        $instance->setValue($value);
        $instance->setDispatcher($dispatcher);
        return $instance->build();
    }
}