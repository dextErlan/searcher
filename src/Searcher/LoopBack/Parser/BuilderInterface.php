<?php

namespace Searcher\LoopBack\Parser;


use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;

interface BuilderInterface {

    /**
     * @param $conditions
     * @return $this
     * @throws InvalidConditionException
     */
    public function build($conditions = null);
}