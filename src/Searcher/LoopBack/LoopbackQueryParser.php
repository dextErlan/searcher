<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 02.04.15
 * Time: 15:05
 */

namespace Searcher\LoopBack;


use Searcher\ArrayUtils;
use Searcher\LoopBack\Filter\FilterParser;
use Searcher\LoopBack\Order\OrderParser;
use Searcher\LoopBack\Page\PageParser;
use Searcher\LoopBack\Search\SearchParser;
use Searcher\QueryParser\QueryParserInterface;

class LoopbackQueryParser implements QueryParserInterface
{
    const FILTER_VAR = "filter";
    const SEARCH_VAR = "q";
    const ORDER_VAR = "order";
    const PAGE_VAR = "page";

    private $filter;
    private $parameters;

    private function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }


    /**
     * @return \Searcher\QueryParser\Filter\Filter[]
     */
    public function getFilters()
    {
        if (!$this->filter) {
            $filterParams = ArrayUtils::get($this->parameters, self::FILTER_VAR, array());
            $this->filter = new FilterParser($filterParams);
        }

        return $this->filter->getFilters();
    }

    public function getSearch()
    {
        $searchString = ArrayUtils::get($this->parameters, self::SEARCH_VAR, null);
        return (new SearchParser($searchString))->get();
    }

    /**
     * @param $parameters
     * @return $this
     */
    public static function create($parameters)
    {
        return new static($parameters);
    }

    public function getOrder()
    {
        $orderParams = ArrayUtils::get($this->parameters, self::ORDER_VAR, null);
        return (new OrderParser($orderParams))->get();
    }

    /**
     * @return \Searcher\QueryParser\Page\Page
     */
    public function getPage()
    {
        $pageParams = ArrayUtils::get($this->parameters, self::PAGE_VAR, array());
        return (new PageParser($pageParams))->get();
    }
}