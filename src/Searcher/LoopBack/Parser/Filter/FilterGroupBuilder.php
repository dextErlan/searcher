<?php

namespace Searcher\LoopBack\Parser\Filter;


use Searcher\LoopBack\Parser\BuilderInterface;
use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\StringUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FilterGroupBuilder implements BuilderInterface, FilterInterface
{
    /** @var EventDispatcherInterface */
    private $dispatcher = null;

    /** @var FilterGroupConditionBuilder[] */
    private $groups = [];

    /** @param EventDispatcherInterface $dispatcher */
    public function setDispatcher(EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    private function getAvailableGroups()
    {
        return [
            FilterCondition::CONDITION_AND,
            FilterCondition::CONDITION_OR
        ];
    }

    /**
     * @inheritdoc
     * @return FilterGroupConditionBuilder[]
     */
    public function build($conditions = null)
    {
        $groups = [];

        foreach ($conditions as $groupOperator => $filterData) {
            $group = StringUtils::toLower($groupOperator);

            if (!in_array($group, $this->getAvailableGroups())) {
                $group = FilterCondition::CONDITION_AND;
                $filterData = [$groupOperator => $filterData];
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
        $instance->setDispatcher($dispatcher);
        return $instance->build($groups);
    }
}