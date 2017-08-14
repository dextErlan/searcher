<?php

namespace Searcher\Events;

use Searcher\LoopBack\Parser\Pagination\LimitBuilder;
use Symfony\Component\EventDispatcher\Event;

class LimitEvent extends Event
{
    /**
     * @deprecated
     * @see EventNames::LIMIT_EVENT
     */
    const EVENT_NAME = EventNames::LIMIT_EVENT;
    /**
     * @var LimitBuilder
     */
    private $limitBuilder;

    /**
     * @param LimitBuilder $limitBuilder
     */
    public function __construct(LimitBuilder $limitBuilder)
    {
        $this->limitBuilder = $limitBuilder;
    }

    /**
     * @return LimitBuilder
     */
    public function getLimitBuilder()
    {
        return $this->limitBuilder;
    }
}