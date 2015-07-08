<?php

namespace Searcher\LoopBack\Parser\Filter\Condition\CompareCondition;


use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\LoopBack\Parser\Filter\FilterCondition;

class LtCondition extends AbstractCondition
{
    public function getOperator()
    {
        return FilterCondition::CONDITION_LT;
    }

    /**
     * @return $this
     */
    public function build()
    {
        $value = $this->getValue();

        if (is_array($value)) {
            throw new InvalidConditionException('$value must be integer, array given');
        }

        if (!is_numeric($value)) {
            throw new InvalidConditionException('$value must be integer');
        }

        $this->setValue($value);

        return $this;
    }
}