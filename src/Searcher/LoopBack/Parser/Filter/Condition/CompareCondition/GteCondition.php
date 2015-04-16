<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 15.04.15
 * Time: 18:31
 */

namespace Searcher\LoopBack\Parser\Filter\Condition\CompareCondition;


use Searcher\LoopBack\Parser\Filter\FilterCondition;

class GteCondition extends GtCondition
{
    public function getOperator()
    {
        return FilterCondition::CONDITION_GTE;
    }
}