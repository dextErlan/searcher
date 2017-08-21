<?php

namespace Searcher\LoopBack\Parser\Filter\Condition\CompareCondition;


use Searcher\LoopBack\Parser\Filter\FilterCondition;

class LteCondition extends EqCondition
{

    public function getOperator()
    {
        return FilterCondition::CONDITION_LTE;
    }
}