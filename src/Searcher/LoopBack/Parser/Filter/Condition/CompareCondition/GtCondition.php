<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 15.04.15
 * Time: 18:30
 */

namespace Searcher\LoopBack\Parser\Filter\Condition\CompareCondition;


use Searcher\LoopBack\Parser\Filter\FilterCondition;

class GtCondition extends LtCondition
{
    public function getOperator()
    {
        return FilterCondition::CONDITION_GT;
    }
}