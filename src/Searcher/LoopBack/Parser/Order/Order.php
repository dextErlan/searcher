<?php

namespace Searcher\LoopBack\Parser\Order;


use Searcher\StringUtils;

class Order
{

    private $direction;
    private $field;

    public function __construct($field, $direction)
    {
        $this->field = $field;
        $this->direction = StringUtils::toLower($direction);
    }

    /**
     * @return mixed
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }
}