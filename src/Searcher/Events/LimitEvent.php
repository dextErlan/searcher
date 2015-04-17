<?php

namespace Searcher\Events;

use Searcher\LoopBack\Parser\Pagination\LimitBuilder;
use Symfony\Component\EventDispatcher\Event;

class LimitEvent extends Event
{
    const EVENT_NAME = 'searcher.limit';
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