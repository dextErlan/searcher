<?php
namespace Searcher\QueryParser\Page;


class Page implements PageInterface
{

    const DEFAULT_MAX = 100;
    const DEFAULT_MIN = 1;
    private $max = self::DEFAULT_MAX;
    private $min = self::DEFAULT_MIN;
    private $limit;
    private $offset;

    /**
     * @param $limit
     * @param $offset
     */
    public function __construct($limit, $offset)
    {

        $this->limit = $limit;
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param int $max
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param int $min
     */
    public function setMin($min)
    {
        $this->min = $min;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        if ($this->limit >= $this->getMax()) {
            return $this->getMax();
        }
        if ($this->limit <= $this->getMin()) {
            return $this->getMin();
        }
        return $this->limit;
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return $this->offset;
    }

}