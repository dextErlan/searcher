<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 02.04.15
 * Time: 14:59
 */

namespace Searcher\QueryParser;


use Searcher\QueryParser\Filter\FilterInterface;
use Searcher\QueryParser\Order\OrderInterface;
use Searcher\QueryParser\Search\SearchInterface;

interface QueryParserInterface
{
    /**
     * @param $parameters
     * @return $this
     */
    public static function create($parameters);

    /**
     * @return FilterInterface[]
     */
    public function getFilters();

    /**
     * @return SearchInterface
     */
    public function getSearch();

    /**
     * @return OrderInterface[]
     */
    public function getOrder();
    /**
     * @return \Searcher\QueryParser\Page\PageInterface
     */
    public function getPage();
}