<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 16.04.15
 * Time: 11:54
 */

namespace Searcher\LoopBack\Parser\Filter\Condition\CompareCondition;


use Searcher\LoopBack\Parser\Filter\Condition\ConditionInterface;
use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\LoopBack\Parser\Filter\FilterCondition;

class EqCondition implements ConditionInterface
{
    private $field;
    private $value;

    /**
     * @param $field
     * @param $value
     * @throws \Exception
     */
    public function __construct($field, $value)
    {
        $this->field = $field;
        if (is_array($value)) {
            throw new InvalidConditionException();
        }
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getOperator()
    {
        return FilterCondition::CONDITION_EQ;
    }

}