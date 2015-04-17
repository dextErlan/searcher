<?php

namespace Searcher\LoopBack\Parser\Order;


use Searcher\ArrayUtils;
use Searcher\Events\FieldEvent;
use Searcher\Events\OrderEvent;
use Searcher\LoopBack\Parser\BuilderInterface;
use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\StringUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OrderBuilder implements BuilderInterface
{
    const DIRECTION_ASC = 'asc';
    const DIRECTION_DESC = 'desc';

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher = null;
    private $condition;


    public function setCondition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     * @return $this
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    /**
     * @return Order[]
     */
    public function build()
    {

        if (!is_array($this->condition)) {
            return array();
        }
        if (ArrayUtils::isList($this->condition)) {
            return array();
        }

        $result = array();

        foreach ($this->condition as $field => $condition) {
            try {
                $result[] = $this->buildOrder($field, $condition);
            } catch (InvalidConditionException $e) {
                continue;
            }
        }

        return $result;
    }

    private function buildOrder($field, $direction)
    {
        if (is_array($direction)) {
            throw new InvalidConditionException('wrong direction');
        }
        $direction = StringUtils::toLower($direction);
        if (!in_array($direction, array(self::DIRECTION_ASC, self::DIRECTION_DESC))) {
            throw new InvalidConditionException("Wrong direction");
        }

        if ($this->dispatcher) {
            $this->dispatcher->dispatch(OrderEvent::EVENT_NAME, new OrderEvent($field, $direction));
            $this->dispatcher->dispatch(FieldEvent::EVENT_NAME, new FieldEvent($field));
        }

        return new Order($field, $direction);
    }

    /**
     * @param $conditions
     * @param EventDispatcherInterface $dispatcher
     * @return Order[]
     */
    public static function create($conditions, EventDispatcherInterface $dispatcher = null)
    {
        $instance = new static();
        $instance->setCondition($conditions);
        $instance->setEventDispatcher($dispatcher);

        return $instance->build();
    }
}