<?php

namespace Searcher\LoopBack\Parser\Filter\Condition\CompareCondition;


use Searcher\LoopBack\Parser\Filter\FilterCondition;

class GtCondition extends LtCondition
{
    public function getOperator()
    {
        return FilterCondition::CONDITION_GT;
    }
}