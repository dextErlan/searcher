<?php

namespace Searcher\LoopBack\Order;

use Searcher\QueryParser\Order\Order;

class OrderParser
{

    const MARK_DESC = '-';
    const MARK_ASC = '+';
    private $order;


    /**
     * @param $order
     */
    public function __construct($order)
    {
        if (!is_scalar($order)) {
            $order = array();
        } else {
            $order = explode(',', $order);
        }

        $this->order = $order;
    }

    /**
     * @return \Searcher\QueryParser\Order\Order[]
     */
    public function get()
    {
        return $this->parseOrder($this->order);
    }

    /**
     * @param $sign
     * @return string
     */
    public function getDirection($sign)
    {
        switch ($sign) {
            case self::MARK_DESC:
                $orderDirection = Order::DIRECTION_DESC;
                break;

            case self::MARK_ASC:
                $orderDirection = Order::DIRECTION_ASC;
                break;

            default:
                $orderDirection = Order::DIRECTION_ASC;
                break;
        }

        return $orderDirection;
    }

    /**
     * @param array $orderArray
     * @return Order[]
     */
    private function parseOrder(array $orderArray)
    {
        $orderRules = array();

        foreach ($orderArray as $orderField) {
            $sign = mb_substr($orderField, 0, 1);
            // if first letter + or -, cut it
            // todo: maybe throw exception, or skip this field, or skip all fields?
            $orderField = preg_replace('/^[+-]/', '', $orderField);
            $orderDirection = $this->getDirection($sign);
            $orderRules[] = new Order(trim($orderField), $orderDirection);
        }

        return $orderRules;
    }
}