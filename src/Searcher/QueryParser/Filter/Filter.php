<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 03.04.15
 * Time: 13:47
 */

namespace Searcher\QueryParser\Filter;


class Filter implements FilterInterface
{
    /**
     * @var
     */
    private $field;
    /**
     * @var
     */
    private $operator;
    /**
     * @var
     */
    private $value;

    /**
     * @param $field
     * @param $operator
     * @param $value
     */
    public function __construct($field, $operator, $value)
    {

        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
    }

    const OPERATOR_EQ = 'eq';
    const OPERATOR_GT = 'gt';
    const OPERATOR_GTE = 'gte';
    const OPERATOR_LT = 'lt';
    const OPERATOR_LTE = 'lte';

    public static function acceptedOperator(){
        return array(
            self::OPERATOR_EQ,
            self::OPERATOR_GT,
            self::OPERATOR_GTE,
            self::OPERATOR_LT,
            self::OPERATOR_LTE,
        );
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

}