<?php

namespace Searcher\LoopBack\Parser\Filter;


use Searcher\ArrayUtils;
use Searcher\Events\GroupEvent;
use Searcher\LoopBack\Parser\BuilderInterface;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition as CompareCondition;
use Searcher\LoopBack\Parser\Filter\Condition\ConditionInterface;
use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\StringUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FilterGroupConditionBuilder implements BuilderInterface
{
    private $acceptedGroups = array(
        FilterCondition::CONDITION_AND,
        FilterCondition::CONDITION_OR,
    );

    private $group = FilterCondition::CONDITION_AND;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher = null;

    /**
     * @var ConditionInterface[]
     */
    private $conditions = array();

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param $group
     * @return $this
     */
    public function setGroup($group)
    {
        $group = StringUtils::toLower($group);
        $this->group = $group;

        return $this;
    }

    /**
     * todo: change method name
     * @param $parameters
     * @return $this
     */
    public function setConditions($parameters)
    {
        if (!is_array($parameters)) {
            throw new InvalidConditionException('$parameters must be an array');
        }
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return $this
     */
    public function build()
    {
        if (!in_array($this->group, $this->acceptedGroups)) {
            throw new InvalidConditionException('invalid group');
        }

        if ($this->dispatcher) {
            $this->dispatcher->dispatch(GroupEvent::EVENT_NAME, new GroupEvent($this->group));
        }

        $results = array();

        foreach ($this->parameters as $condition => $fieldValues) {
            $operator = null;

            if (is_array($fieldValues) && !ArrayUtils::isList($fieldValues)) {
                $operator = $condition;
            } else {
                $fieldValues = array($condition => $fieldValues);
            }

            foreach ($fieldValues as $field => $value) {

                $builder = new FilterConditionBuilder();
                if ($operator !== null) {
                    $builder->setCompareOperator($operator);
                }
                $builder->setEventDispatcher($this->dispatcher);
                $builder->setConditions($field, $value);

                try {
                    $results[] = $builder->build();

                } catch (InvalidConditionException $e) {
                    continue;
                }
            }

        }

        $this->checkCondition($results);
        $this->conditions = $results;

        return $this;
    }

    private function checkCondition($results)
    {
        if (empty($results)) {
            throw new InvalidConditionException("Conditions empty");
        }

    }

    /**
     * @return Condition\ConditionInterface[]
     */
    public function getConditions()
    {
        $this->checkCondition($this->conditions);

        return $this->conditions;
    }

    /**
     * @param $groupName
     * @param array $conditions
     * @param EventDispatcherInterface $dispatcher
     * @return static
     */
    public static function create($groupName, array $conditions, EventDispatcherInterface $dispatcher = null)
    {
        $instance = new static();
        $instance->setGroup($groupName);
        $instance->setConditions($conditions);
        $instance->setEventDispatcher($dispatcher);

        return $instance->build();
    }
}