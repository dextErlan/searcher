<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 03.04.15
 * Time: 13:47
 */

namespace Searcher\QueryParser\Filter;


interface FilterInterface {

    public function getField();
    public function getOperator();
    public function getValue();
}