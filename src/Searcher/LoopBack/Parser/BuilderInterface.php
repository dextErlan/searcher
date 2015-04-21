<?php

namespace Searcher\LoopBack\Parser;


use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;

interface BuilderInterface {

    /**
     * @return $this
     * @throws InvalidConditionException
     */
    public function build();
}