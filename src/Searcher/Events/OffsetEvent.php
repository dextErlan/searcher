<?php

namespace Searcher\Events;

use Searcher\LoopBack\Parser\Pagination\OffsetBuilder;
use Symfony\Contracts\EventDispatcher\Event;

class OffsetEvent extends Event
{
    /**
     * @deprecated
     * @see EventNames::OFFSET_EVENT
     */
    const EVENT_NAME = EventNames::OFFSET_EVENT;
    /**
     * @var OffsetBuilder
     */
    private $offsetBuilder;

    /**
     * @param OffsetBuilder $offsetBuilder
     */
    public function __construct(OffsetBuilder $offsetBuilder)
    {
        $this->offsetBuilder = $offsetBuilder;
    }

    /**
     * @return OffsetBuilder
     */
    public function getOffsetBuilder()
    {
        return $this->offsetBuilder;
    }

}