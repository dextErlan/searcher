<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 15.04.15
 * Time: 18:11
 */

namespace Searcher\LoopBack\Parser;


use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\LoopBack\Parser\Filter\FilterGroupBuilder;
use Searcher\LoopBack\Parser\Filter\FilterGroupConditionBuilder;
use Searcher\LoopBack\Parser\Order\Order;
use Searcher\LoopBack\Parser\Order\OrderBuilder;
use Searcher\LoopBack\Parser\Pagination\LimitBuilder;
use Searcher\LoopBack\Parser\Pagination\OffsetBuilder;
use Searcher\StringUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Builder implements BuilderInterface
{

    private $mapping = array(
        self::FILTER_WHERE => array(
            "className" => FilterGroupBuilder::class,
            "field" => "filters"
        ),
        self::FILTER_ORDER => array(
            "className" => OrderBuilder::class,
            "field" => "orders"
        ),
        self::FILTER_LIMIT => array(
            "className" => LimitBuilder::class,
            "field" => "limit"
        ),
        self::FILTER_OFFSET => array(
            "className" => OffsetBuilder::class,
            "field" => "offset"
        ),
    );

    const FILTER_WHERE = 'where';
    const FILTER_ORDER = 'order';
    const FILTER_LIMIT = 'limit';
    const FILTER_OFFSET = 'skip';

    /**
     * @var
     */
    private $conditions;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct($conditions, EventDispatcherInterface $dispatcher = null)
    {
        $this->conditions = $conditions;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @var LimitBuilder
     */
    private $limit = LimitBuilder::LIMIT_DEFAULT;

    /**
     * @var OffsetBuilder
     */
    private $offset = OffsetBuilder::OFFSET_DEFAULT;

    /**
     * @var Order[]
     */
    private $orders = array();

    /**
     * @var FilterGroupConditionBuilder[]
     */
    private $filters = array();

    /**
     * @return $this
     * @throws InvalidConditionException
     */
    public function build()
    {
        if (!is_array($this->conditions)) {
            return $this;
        }

        foreach ($this->conditions as $key => $values) {
            $key = StringUtils::toLower($key);
            if (!isset($this->mapping[$key])) {
                continue;
            }

            $className = $this->mapping[$key]["className"];

            $data = forward_static_call_array(
                array($className, "create"),
                array($values, $this->dispatcher)
            );

            $fieldName = $this->mapping[$key]["field"];
            $this->$fieldName = $data;
        }

        return $this;
    }

    /**
     * @return Order[]
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @return Filter\FilterGroupConditionBuilder[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return Pagination\LimitBuilder
     */
    public function getLimit()
    {
        if (is_object($this->limit)) {
            return $this->limit->getLimit();
        }

        return $this->limit;
    }

    /**
     * @return Pagination\OffsetBuilder
     */
    public function getOffset()
    {
        if (is_object($this->offset)) {
            return $this->offset->getOffset();
        }

        return $this->offset;
    }

}