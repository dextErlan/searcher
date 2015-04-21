<?php

namespace Searcher\LoopBack\Parser\Filter;


use Searcher\LoopBack\Parser\BuilderInterface;
use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\StringUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FilterGroupBuilder implements BuilderInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher = null;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @var array
     */
    private $conditions = array();

    /**
     * @var FilterGroupConditionBuilder[]
     */
    private $groups = array();

    private function getAvailableGroups()
    {
        return array(
            FilterCondition::CONDITION_AND,
            FilterCondition::CONDITION_OR
        );
    }

    /**
     * @param  $conditions
     */
    public function setGroupCondition($conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @return FilterGroupConditionBuilder[]
     */
    public function build()
    {
        $groupConditions = $this->conditions;
        $groups = array();

        foreach ($groupConditions as $groupOperator => $filterData) {
            $group = StringUtils::toLower($groupOperator);

            if (!in_array($group, $this->getAvailableGroups())) {
                $group = FilterCondition::CONDITION_AND;
                $filterData = array($groupOperator => $filterData);
            }

            $conditionBuilder = new FilterGroupConditionBuilder();
            $conditionBuilder->setGroup($group);
            $conditionBuilder->setEventDispatcher($this->dispatcher);
            $conditionBuilder->setConditions($filterData);
            try {
                $groups[] = $conditionBuilder->build();
            } catch (InvalidConditionException $e) {
                continue;
            }

        }
        $this->groups = $groups;

        return $this->groups;
    }

    /**
     * @return FilterGroupConditionBuilder[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    public static function create($groups, EventDispatcherInterface $dispatcher = null)
    {
        $instance = new static();
        $instance->setGroupCondition($groups);
        $instance->setDispatcher($dispatcher);
        return $instance->build();
    }
}