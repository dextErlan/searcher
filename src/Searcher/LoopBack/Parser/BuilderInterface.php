<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 17.04.15
 * Time: 10:11
 */

namespace Searcher\LoopBack\Parser;


use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;

interface BuilderInterface {

    /**
     * @return $this
     * @throws InvalidConditionException
     */
    public function build();
}