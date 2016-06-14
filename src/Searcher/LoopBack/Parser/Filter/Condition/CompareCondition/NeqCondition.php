<?php

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
     * @inheritdoc
     */
    public function build($conditions = null)
    {
        if (is_array($this->getValue())) {
            throw new InvalidConditionException("value must be scalar");
        }

        return $this;
    }
}