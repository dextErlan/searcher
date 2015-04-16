<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 15.04.15
 * Time: 18:30
 */

namespace Searcher\LoopBack\Parser\Filter\Condition\CompareCondition;


use Searcher\ArrayUtils;
use Searcher\LoopBack\Parser\Filter\Condition\ConditionInterface;
use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\LoopBack\Parser\Filter\FilterCondition;

class InqCondition implements ConditionInterface
{

    private $field;
    private $values = array();

    /**
     * @param $field
     * @param $values
     */
    public function __construct($field, $values)
    {

        $this->field = $field;
        if (!is_array($values)) {
            $values = array($values);
        }
        if (!ArrayUtils::isList($values)) {
            throw new InvalidConditionException('$values must be anb array without hash keys');
        }
        if (empty($values)) {
            throw new InvalidConditionException();
        }
        $this->values = $values;
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
        return $this->values;
    }

    public function getOperator()
    {
        return FilterCondition::CONDITION_IN;
    }
}