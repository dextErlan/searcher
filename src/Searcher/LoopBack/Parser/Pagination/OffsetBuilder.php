<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 17.04.15
 * Time: 15:10
 */

namespace Searcher\LoopBack\Parser\Pagination;


use Searcher\Events\OffsetEvent;
use Searcher\LoopBack\Parser\BuilderInterface;
use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OffsetBuilder implements BuilderInterface
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
     * @return $this
     * @throws InvalidConditionException
     */
    public function build()
    {
        if ($this->dispatcher) {
            $this->dispatcher->dispatch(OffsetEvent::EVENT_NAME, new OffsetEvent($this));
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