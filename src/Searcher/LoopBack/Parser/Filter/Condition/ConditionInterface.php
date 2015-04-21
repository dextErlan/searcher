<?php

namespace Searcher\LoopBack\Parser\Filter\Condition;


interface ConditionInterface {
    public function getField();
    public function getValue();
    public function getOperator();
}