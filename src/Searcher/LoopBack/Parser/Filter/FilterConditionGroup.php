<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 15.04.15
 * Time: 18:22
 */

namespace Searcher\LoopBack\Parser\Filter;


use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\StringUtils;

class FilterConditionGroup
{
    const GROUP_AND = 'and';
    const GROUP_OR = 'or';

    private $condition;

    /**
     * @param array $groupCondition
     */
    public function __construct($groupCondition)
    {
        if (!is_array($groupCondition)) {
            $groupCondition = array();
        }
        $condition = $this->initConditions($groupCondition);
        $this->condition = $condition;
    }


    private function getAvailableGroups()
    {
        return array(
            self::GROUP_AND,
            self::GROUP_OR
        );
    }

    /**
     * @param $groupConditions
     * @return FilterConditionBuilder[]
     */
    private function initConditions($groupConditions)
    {
        $groups = array();

        foreach ($groupConditions as $groupOperator => $filterData) {
            $group = $rawOperator = StringUtils::toLower($groupOperator);
            try {
                if (!in_array($rawOperator, $this->getAvailableGroups())) {
                    $group = self::GROUP_AND;
                    $filterData = array($groupOperator => $filterData);
                }
                $groups[] = new FilterConditionBuilder($group, $filterData);
            } catch (InvalidConditionException $e) {
                continue;
            }
        }

        if (empty($groups)) {
            throw new InvalidConditionException();
        }

        return $groups;
    }

    /**
     * @return FilterConditionBuilder[]
     */
    public function getConditions()
    {
        return $this->condition;
    }

}