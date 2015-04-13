<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 13.04.15
 * Time: 17:25
 */

namespace Searcher\Transformer;


use Searcher\QueryParser\Filter\Filter;
use Searcher\QueryParser\QueryParserInterface;

class ElasticSearchTransformer
{
    const FILTER_OR = 'or';
    const FILTER_AND = 'and';
    const FILTER_RANGE = 'range';
    const FILTER_TERM = 'term';
    const FILTER_TERMS = 'terms';
    const ORDER_TERM = 'order';

    /**
     * @var QueryParserInterface
     */
    private $queryParser;

    /**
     * @param QueryParserInterface $queryParser
     */
    public function __construct(QueryParserInterface $queryParser)
    {

        $this->queryParser = $queryParser;
    }

    public function getQuery()
    {
        $query = $this->parseLimit();
        $filters = $this->parseFilters();
        if (!empty($filters)) {
            $query["query"]['filtered']["filter"] = $filters;
        }
        $order = $this->parseOrder();

        if (!empty($order)) {
            $query["sort"] = $order;
        }

        $search = $this->getSearch();
        if ($search) {
            $query["query"]['filtered']["match"] = $search;
        }

        return $query;
    }

    private function parseFilters()
    {
        $filters = array();
        foreach ($this->queryParser->getFilters() as $filter) {
            if ($filter->getOperator() === Filter::OPERATOR_EQ) {
                if (is_array($filter->getValue())) {
                    $filters[][self::FILTER_TERMS] = $filter->getValue();
                } else {
                    $filters[][self::FILTER_TERM] = $filter->getValue();
                }
                continue;
            }
            if (is_array($filter->getValue())) {
                foreach ($filter->getValue() as $value) {
                    $filters[][self::FILTER_RANGE][$filter->getField()][$filter->getOperator()] = $value;
                }
                continue;
            }
            $filters[][self::FILTER_RANGE][$filter->getField()][$filter->getOperator()] = $filter->getValue();
        }
        return $filters;
    }

    private function parseOrder()
    {
        $sorts = array();
        foreach ($this->queryParser->getOrder() as $sort) {
            $fieldName = $sort->getField();
            $sorts[] = array(
                $fieldName => array(
                    self::ORDER_TERM => $sort->getDirection()
                )
            );
        }
        return $sorts;
    }

    private function parseLimit()
    {
        return array(
            "from" => $this->queryParser->getPage()->getOffset(),
            "size" => $this->queryParser->getPage()->getLimit(),
        );
    }

    private function getSearch()
    {
        $query = $this->queryParser->getSearch();
        if (!$query->getTerm()) {
            return null;
        }
        return array("_all" => $query->getTerm());
    }

}