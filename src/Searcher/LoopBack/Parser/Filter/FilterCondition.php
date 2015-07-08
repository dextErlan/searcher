<?php

namespace Searcher\LoopBack\Parser\Filter;

class FilterCondition {

    /**
     * Logical AND operator
     */
    const CONDITION_AND = 'and';
    /**
     * Logical OR operator
     */
    const CONDITION_OR = 'or';
    /**
     * Numerical greater than (>); Valid only for numerical and date values.
     */
    const CONDITION_GT = 'gt';
    /**
     * Numerical greater than or equal (>=). Valid only for numerical and date values.
     */
    const CONDITION_GTE = 'gte';
    /**
     * Numerical less than (<); . Valid only for numerical and date values.
     */
    const CONDITION_LT = 'lt';
    /**
     * Numerical less than or equal (<=). Valid only for numerical and date values.
     */
    const CONDITION_LTE = 'lte';
    /**
     * In an array of values.
     */
    const CONDITION_IN = 'inq';
    /**
     * Not in an array of values.
     */
    const CONDITION_NIN = 'nin';
    /**
     * Not equal (!=)
     */
    const CONDITION_NEQ = 'neq';
    /**
     * Equal (=)
     */
    const CONDITION_EQ = 'eq';
    /**
     * Like 
     */
    const CONDITION_LIKE = 'like';

    public static function getConditions(){
        $reflection = new \ReflectionObject(new self);
        return array_values($reflection->getConstants());
    }
}