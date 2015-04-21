<?php

namespace Searcher\LoopBack\Parser\Filter\Condition\CompareCondition;


use Searcher\LoopBack\Parser\Filter\FilterCondition;

class NinCondition extends InqCondition
{

    public function getOperator()
    {
        return FilterCondition::CONDITION_NIN;
    }
}