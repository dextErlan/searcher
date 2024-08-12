<?php

namespace Searcher\Events;


use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\AbstractCondition;
use Symfony\Contracts\EventDispatcher\Event;

class ConditionEvent extends Event
{
    /**
     * @deprecated
     * @see EventNames::CONDITION_PRE_POPULATE_EVENT
     */
    const EVENT_NAME = EventNames::CONDITION_PRE_POPULATE_EVENT;
    /**
     * @var AbstractCondition
     */
    private $condition;

    /**
     * @param AbstractCondition $condition
     */
    public function __construct(AbstractCondition $condition)
    {
        $this->condition = $condition;
    }

    /**
     * @return AbstractCondition
     */
    public function getCondition()
    {
        return $this->condition;
    }
}