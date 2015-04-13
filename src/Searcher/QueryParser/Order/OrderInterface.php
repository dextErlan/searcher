<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 02.04.15
 * Time: 16:08
 */

namespace Searcher\QueryParser\Order;


interface OrderInterface
{
    public function getField();
    public function getDirection();
}