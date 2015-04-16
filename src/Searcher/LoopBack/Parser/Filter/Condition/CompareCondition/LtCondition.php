<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 15.04.15
 * Time: 18:31
 */

namespace Searcher\LoopBack\Parser\Filter\Condition\CompareCondition;


use Searcher\LoopBack\Parser\Filter\Condition\ConditionInterface;
use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\LoopBack\Parser\Filter\FilterCondition;

class LtCondition implements ConditionInterface
{
    /**
     * @var
     */
    private $field;
    /**
     * @var int|string
     */
    private $value;

    /**
     * @param $field
     * @param $value
     */
    public function __construct($field, $value)
    {
        if (is_array($value)) {
            throw new InvalidConditionException('$value must be integer, array given');
        }
        if (!is_numeric($value)) {
            throw new InvalidConditionException('$value must be integer');
        }
        $this->field = $field;
        $this->value = (int) $value;

    }

    public function getField()
    {
        return $this->field;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getOperator()
    {
        return FilterCondition::CONDITION_LT;
    }
}