<?php

namespace Searcher\Events;

use Searcher\LoopBack\Parser\Pagination\OffsetBuilder;
use Symfony\Component\EventDispatcher\Event;

class OffsetEvent extends Event
{
    const EVENT_NAME = 'searcher.offset';
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