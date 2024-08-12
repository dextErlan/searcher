<?php

namespace Searcher\LoopBack\Parser\Filter\Condition\CompareCondition;


use Searcher\Events\ConditionEvent;
use Searcher\Events\EventNames;
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
     * @param EventDispatcherInterface $dispatcher
     * @return AbstractCondition
     */
    public static function create($field, $value, EventDispatcherInterface $dispatcher = null)
    {
        $instance = new static();
        $instance->setField($field);
        $instance->setValue($value);
        $instance->setDispatcher($dispatcher);
        if ($dispatcher) {
            $dispatcher->dispatch(new ConditionEvent($instance), EventNames::CONDITION_PRE_POPULATE_EVENT);
        }

        $instance = $instance->build();
        if ($dispatcher) {
            $dispatcher->dispatch(new ConditionEvent($instance), EventNames::CONDITION_POST_POPULATE_EVENT);
        }

        return $instance;
    }
}