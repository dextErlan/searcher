<?php

namespace Searcher\LoopBack\Parser\Pagination;


use Searcher\Events\OffsetEvent;
use Searcher\LoopBack\Parser\BuilderInterface;
use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\LoopBack\Parser\Filter\FilterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OffsetBuilder implements BuilderInterface, FilterInterface
{
    const OFFSET_DEFAULT = 0;
    private $offset = 0;
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
    public function getOffset()
    {
        return (int) $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        if (!is_numeric($this->offset)) {
            $offset = self::OFFSET_DEFAULT;
        }

        $this->offset = (int) $offset;
    }

    /**
     * @inheritdoc
     * @return $this
     * @throws InvalidConditionException
     */
    public function build($conditions = null)
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(new OffsetEvent($this), OffsetEvent::EVENT_NAME);
        }

        return $this;
    }


    /**
     * @param $offset
     * @param EventDispatcherInterface $dispatcher
     */
    public static function create($offset, EventDispatcherInterface $dispatcher = null)
    {
        $instance = new static();
        $instance->setDispatcher($dispatcher);
        $instance->setOffset($offset);
        return $instance->build();
    }
}