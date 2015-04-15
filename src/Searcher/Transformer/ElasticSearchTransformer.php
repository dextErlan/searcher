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
            $value = $filter->getValue();
            $operator = $filter->getOperator();
            $field = $filter->getField();

            if ($operator === Filter::OPERATOR_EQ) {
                $filters[] = $this->getTermFilter($field, $value);
                continue;
            }

            foreach ($this->getRangeFilter($field, $value, $operator) as $row) {
                $filters[] = $row;
            }
        }

        return $filters;
    }

    private function getTermFilter($field, $value)
    {
        if (is_array($value)) {
            return array(
                self::FILTER_TERMS => array($field => $value)
            );
        }

        return array(
            self::FILTER_TERM => array($field => $value)
        );
    }

    private function getRangeFilter($field, $value, $operator)
    {
        $array = array();

        if (is_array($value)) {
            foreach ($value as $val) {
                $array[] = array(
                    self::FILTER_RANGE => array(
                        $field => array($operator => $val)
                    )
                );
            }

            return $array;
        }

        return array(
            array(
                self::FILTER_RANGE => array(
                    $field => array($operator => $value)
                )
            )
        );
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