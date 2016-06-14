<?php

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
    const FILTER_WHERE = 'where';
    const FILTER_ORDER = 'order';
    const FILTER_LIMIT = 'limit';
    const FILTER_OFFSET = 'skip';

    private static $mapping = [
        self::FILTER_WHERE => [
            'className' => FilterGroupBuilder::class,
            'field' => 'filters'
        ],
        self::FILTER_ORDER => [
            'className' => OrderBuilder::class,
            'field' => 'orders'
        ],
        self::FILTER_LIMIT => [
            'className' => LimitBuilder::class,
            'field' => 'limit'
        ],
        self::FILTER_OFFSET => [
            'className' => OffsetBuilder::class,
            'field' => 'offset'
        ],
    ];

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var LimitBuilder */
    private $limit = LimitBuilder::LIMIT_DEFAULT;

    /** @var OffsetBuilder */
    private $offset = OffsetBuilder::OFFSET_DEFAULT;

    /** @var Order[] */
    private $orders = [];

    /** @var FilterGroupConditionBuilder[] */
    private $filters = [];

    public function __construct(EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function build($conditions = null)
    {
        if (!is_array($conditions)) {
            return $this;
        }

        foreach ($conditions as $key => $values) {
            $key = StringUtils::toLower($key);
            if (!isset(self::$mapping[$key])) {
                continue;
            }

            $className = self::$mapping[$key]['className'];

            /** @var BuilderInterface $builder */
            $builder = $className::{'create'}($values, $this->dispatcher);

            $fieldName = self::$mapping[$key]['field'];

            $this->$fieldName = $builder;
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