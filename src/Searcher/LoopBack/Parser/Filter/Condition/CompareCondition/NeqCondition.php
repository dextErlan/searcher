<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 16.04.15
 * Time: 11:54
 */

namespace Searcher\LoopBack\Parser\Filter\Condition\CompareCondition;


use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\LoopBack\Parser\Filter\FilterCondition;

class NeqCondition extends AbstractCondition
{
    public function getOperator()
    {
        return FilterCondition::CONDITION_NEQ;
    }

    /**
     * @return $this
     */
    public function build()
    {
        if (is_array($this->getValue())) {
            throw new InvalidConditionException("value must be scalar");
        }

        return $this;
    }
}