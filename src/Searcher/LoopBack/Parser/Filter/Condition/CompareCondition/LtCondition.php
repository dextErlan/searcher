<?php

namespace Searcher\LoopBack\Parser\Filter\Condition\CompareCondition;


use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\LoopBack\Parser\Filter\FilterCondition;

class LtCondition extends EqCondition
{
    public function getOperator()
    {
        return FilterCondition::CONDITION_LT;
    }
}