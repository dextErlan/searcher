<?php

namespace Searcher\Events;


use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\AbstractCondition;
use Symfony\Component\EventDispatcher\Event;

class ConditionEvent extends Event
{
    const EVENT_NAME = 'searcher.condition';
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