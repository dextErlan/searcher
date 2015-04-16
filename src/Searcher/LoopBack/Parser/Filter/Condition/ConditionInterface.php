<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 16.04.15
 * Time: 12:15
 */

namespace Searcher\LoopBack\Parser\Filter\Condition;


interface ConditionInterface {
    public function getField();
    public function getValue();
    public function getOperator();
}