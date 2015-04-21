<?php

namespace Searcher\LoopBack\Parser\Pagination;


use Searcher\Events\LimitEvent;
use Searcher\LoopBack\Parser\BuilderInterface;
use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class LimitBuilder implements BuilderInterface
{

    const LIMIT_DEFAULT = 25;
    private $limit = 25;

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
     * @return int
     */
    public function getLimit()
    {
        return (int) $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        if (!is_numeric($this->limit)) {
            $limit = self::LIMIT_DEFAULT;
        }

        $this->limit = (int) $limit;
    }

    /**
     * @return $this
     * @throws InvalidConditionException
     */
    public function build()
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(LimitEvent::EVENT_NAME, new LimitEvent($this));
        }

        return $this;
    }

    /**
     * @param $limit
     * @param EventDispatcherInterface $dispatcher
     */
    public static function create($limit, EventDispatcherInterface $dispatcher = null)
    {
        $instance = new static();
        $instance->setDispatcher($dispatcher);
        $instance->setLimit($limit);

        return $instance->build();
    }
}