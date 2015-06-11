<?php

namespace Searcher\LoopBack\Parser\Filter;


use Searcher\Events\BeforeConditionBuildEvent;
use Searcher\Events\FieldEvent;
use Searcher\Events\OperatorEvent;
use Searcher\LoopBack\Parser\BuilderInterface;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition as CompareCondition;
use Searcher\LoopBack\Parser\Filter\Condition\ConditionInterface;
use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\StringUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FilterConditionBuilder implements BuilderInterface
{
    private $comparesMap = array(
        FilterCondition::CONDITION_LTE => "\\Searcher\\LoopBack\\Parser\\Filter\\Condition\\CompareCondition\\LteCondition",
        FilterCondition::CONDITION_LT => "\\Searcher\\LoopBack\\Parser\\Filter\\Condition\\CompareCondition\\LtCondition",
        FilterCondition::CONDITION_GT => "\\Searcher\\LoopBack\\Parser\\Filter\\Condition\\CompareCondition\\GtCondition",
        FilterCondition::CONDITION_GTE => "\\Searcher\\LoopBack\\Parser\\Filter\\Condition\\CompareCondition\\GteCondition",
        FilterCondition::CONDITION_IN => "\\Searcher\\LoopBack\\Parser\\Filter\\Condition\\CompareCondition\\InqCondition",
        FilterCondition::CONDITION_NIN => "\\Searcher\\LoopBack\\Parser\\Filter\\Condition\\CompareCondition\\NinCondition",
        FilterCondition::CONDITION_NEQ => "\\Searcher\\LoopBack\\Parser\\Filter\\Condition\\CompareCondition\\NeqCondition",
        FilterCondition::CONDITION_EQ => "\\Searcher\\LoopBack\\Parser\\Filter\\Condition\\CompareCondition\\EqCondition",
        FilterCondition::CONDITION_LIKE => "\\Searcher\\LoopBack\\Parser\\Filter\\Condition\\CompareCondition\\LikeCondition",
    );

    private $compareOperator = FilterCondition::CONDITION_EQ;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var string
     */
    private $field;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param $condition
     * @return $this
     */
    public function setCompareOperator($condition)
    {
        $condition = StringUtils::toLower($condition);
        $this->compareOperator = $condition;

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function setConditions($field, $value)
    {
        $this->field = $field;
        $this->value = $value;

        return $this;
    }

    /**
     * @return ConditionInterface
     * @throws InvalidConditionException
     */
    public function build()
    {
        $condition = StringUtils::toLower($this->compareOperator);

        if ($this->dispatcher) {
            $this->dispatcher->dispatch(
                OperatorEvent::EVENT_NAME,
                new OperatorEvent($condition, $this->field, $this->value)
            );
        }

        if ($condition == FilterCondition::CONDITION_EQ && is_array($this->value)) {
            $condition = FilterCondition::CONDITION_IN;
        }

        if (!isset($this->comparesMap[$condition])) {
            throw new InvalidConditionException("Operator doesn't supported");
        }

        if ($this->dispatcher) {
            $this->dispatcher->dispatch(FieldEvent::EVENT_NAME, new FieldEvent($this->field));
        }

        $className = $this->comparesMap[$condition];
        //todo:
        /* @var $object CompareCondition\AbstractCondition */
        $object = forward_static_call_array(
            array($className, "create"),
            array($this->field, $this->value, $this->dispatcher)
        );

        return $object;
    }

    /**
     * @param $compareOperator
     * @param $field
     * @param $value
     * @param EventDispatcherInterface $dispatcher
     * @return ConditionInterface
     */
    public static function create($compareOperator, $field, $value, EventDispatcherInterface $dispatcher = null)
    {
        $instance = new static();
        $instance->setCompareOperator($compareOperator);
        $instance->setConditions($field, $value);
        $instance->setEventDispatcher($dispatcher);

        return $instance->build();
    }

}