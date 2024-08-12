<?php

namespace Searcher\LoopBack\Parser\Filter;


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
    private $comparesMap = [
        FilterCondition::CONDITION_LTE => CompareCondition\LteCondition::class,
        FilterCondition::CONDITION_LT => CompareCondition\LtCondition::class,
        FilterCondition::CONDITION_GT => CompareCondition\GtCondition::class,
        FilterCondition::CONDITION_GTE => CompareCondition\GteCondition::class,
        FilterCondition::CONDITION_IN => CompareCondition\InqCondition::class,
        FilterCondition::CONDITION_NIN => CompareCondition\NinCondition::class,
        FilterCondition::CONDITION_NEQ => CompareCondition\NeqCondition::class,
        FilterCondition::CONDITION_EQ => CompareCondition\EqCondition::class,
        FilterCondition::CONDITION_LIKE => CompareCondition\LikeCondition::class,
    ];

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
     * @inheritdoc
     * @return ConditionInterface
     * @throws InvalidConditionException
     */
    public function build($conditions = null)
    {
        $condition = StringUtils::toLower($this->compareOperator);

        if ($this->dispatcher) {
            $this->dispatcher->dispatch(
                new OperatorEvent($condition, $this->field, $this->value),
                OperatorEvent::EVENT_NAME
            );
        }

        if ($condition == FilterCondition::CONDITION_EQ && is_array($this->value)) {
            $condition = FilterCondition::CONDITION_IN;
        }

        if (!isset($this->comparesMap[$condition])) {
            throw new InvalidConditionException("Operator doesn't supported");
        }

        if ($this->dispatcher) {
            $this->dispatcher->dispatch(new FieldEvent($this->field), FieldEvent::EVENT_NAME);
        }

        $className = $this->comparesMap[$condition];

        return $className::{'create'}($this->field, $this->value, $this->dispatcher);
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