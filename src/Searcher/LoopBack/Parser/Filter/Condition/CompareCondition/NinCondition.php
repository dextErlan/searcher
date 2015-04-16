<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 15.04.15
 * Time: 18:30
 */

namespace Searcher\LoopBack\Parser\Filter\Condition\CompareCondition;


use Searcher\LoopBack\Parser\Filter\FilterCondition;

class NinCondition extends InqCondition
{

    public function getOperator()
    {
        return FilterCondition::CONDITION_NIN;
    }
}