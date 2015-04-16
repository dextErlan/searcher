<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 16.04.15
 * Time: 11:04
 */

namespace Searcher\LoopBack\Parser\Filter;


use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition as CompareCondition;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\EqCondition;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\InqCondition;
use Searcher\LoopBack\Parser\Filter\Condition\ConditionInterface;
use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\StringUtils;

class FilterConditionBuilder
{
    private $acceptedGroups = array(
        FilterCondition::CONDITION_AND,
        FilterCondition::CONDITION_OR,
    );

    private $group = FilterCondition::CONDITION_AND;

    private $conditionOperationsMap = array(
        FilterCondition::CONDITION_LTE => CompareCondition\LteCondition::class,
        FilterCondition::CONDITION_LT => CompareCondition\LtCondition::class,
        FilterCondition::CONDITION_GT => CompareCondition\GtCondition::class,
        FilterCondition::CONDITION_GTE => CompareCondition\GteCondition::class,
        FilterCondition::CONDITION_IN => CompareCondition\InqCondition::class,
        FilterCondition::CONDITION_NIN => CompareCondition\NinCondition::class,
        FilterCondition::CONDITION_NEQ => CompareCondition\NeqCondition::class,
    );

    private $conditions;

    /**
     * @param $group
     * @param $parameters
     */
    public function __construct($group, $parameters)
    {
        $this->group = $this->initGroup($group);

        if (!is_array($parameters)) {
            throw new InvalidConditionException('$parameters must be an array');
        }

        $conditions = $this->initConditions($parameters);

        if (empty($conditions)) {
            throw new InvalidConditionException();
        }
        $this->conditions = $conditions;
    }

    private function initGroup($group)
    {
        $group = StringUtils::toLower($group);
        if (in_array($group, $this->acceptedGroups)) {
            return $group;
        }
        throw new InvalidConditionException('invalid group');
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }


    /**
     * @return ConditionInterface[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param array $rawParameters
     * @return Condition\ConditionInterface[]
     */
    private function initConditions(array $rawParameters)
    {
        $array = array();
        foreach ($rawParameters as $operator => $parameters) {

            $operator = StringUtils::toLower($operator);

            try {
                if (!isset($this->conditionOperationsMap[$operator])) {
                    if (!is_array($parameters)) {
                        $array[] = new EqCondition($operator, $parameters);
                        continue;
                    }
                    $array[] = new InqCondition($operator, $parameters);
                    continue;
                }
                $conditions = $this->buildConditions($operator, $parameters);

            } catch (InvalidConditionException $e) {
                continue;
            }
            foreach ($conditions as $condition) {
                $array[] = $condition;
            }
        }
        if(empty($array)){
            throw new InvalidConditionException();
        }

        return $array;
    }

    /**
     * @param $operator
     * @param $parameters
     * @return ConditionInterface[]
     */
    private function buildConditions($operator, $parameters)
    {
        /* @var $resultArray ConditionInterface[] */
        $resultArray = array();
        if (!is_array($parameters)) {
            throw new InvalidConditionException('$parameters must be an array');
        }
        $className = $this->conditionOperationsMap[$operator];

        foreach ($parameters as $field => $value) {
            try {
                $resultArray[] = new $className($field, $value);
            } catch (InvalidConditionException $e) {
                continue;
            }
        }

        return $resultArray;
    }

}