<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 02.04.15
 * Time: 16:14
 */

namespace Searcher\QueryParser\Order;

class Order implements OrderInterface
{

    const DIRECTION_ASC = 'ASC';
    const DIRECTION_DESC = 'DESC';

    private $field;
    private $direction;

    /**
     * @param $field
     * @param $direction
     */
    public function __construct($field, $direction)
    {

        $this->field = $field;
        $this->direction = $direction;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param mixed $direction
     * @return Order
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
        return $this;
    }

    /**
     * @param mixed $field
     * @return Order
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }
}