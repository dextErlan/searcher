<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 03.04.15
 * Time: 13:24
 */

namespace Searcher\LoopBack\Filter;


use Searcher\ArrayUtils;
use Searcher\QueryParser\Filter\Filter;

class FilterParser
{

    private $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    const FILTER_WHERE = 'where';
    const FILTER_VALUES_SEPARATOR = ',';

    /**
     * @return \Searcher\QueryParser\Filter\Filter[]
     */
    public function getFilters()
    {

        $rawFilter = ArrayUtils::get($this->filters, static::FILTER_WHERE, array());
        if (!is_array($rawFilter)) {
            $rawFilter = array();
        }
        $filters = $this->parseWhere($rawFilter);

        return $filters;
    }

    /**
     * Parse Where
     * @param array $filterWhere
     * @return Filter[]
     */
    private function parseWhere(array $filterWhere)
    {
        /*
         * our filters:
         *  filter[where][field] = 123
         *  filter[where][field][lt] = 123
         *  filter[where][field] = 123,45,67
         *  filter[where][field] = 123,45,67
         */

        $filters = array();
        foreach ($filterWhere as $filterKey => $filterValue) {
            // todo: maybe use doctrine collection?
            foreach ($this->parseKeyValue($filterKey, $filterValue) as $filter) {
                if ($filter instanceof Filter) {
                    $filters[] = $filter;
                }
            }
        }
        return $filters;
    }

    private function parseKeyValue($filterKey, $filterValue)
    {
        $filters = array();
        if (is_array($filterValue)) {
            return $this->parseValueArray($filterKey, $filterValue);
        }
        $filters[] = new Filter($filterKey, Filter::OPERATOR_EQ, $this->parseValue($filterValue));

        return $filters;
    }

    /**
     * parse fields for filter (ex: field[eq]=asd&field[gt]=sdjh)
     * @param $filterKey
     * @param array $filterValue
     * @return Filter[]
     */
    private function parseValueArray($filterKey, array $filterValue)
    {
        // todo: resolve situation asd>321 and zxc=123 (operator must be only eq in this case)
        // todo: resolve situation asd>asd (value must be numeric for compare)
        $filtersObjects = array();
        foreach ($filterValue as $operator => $values) {
            if (!is_scalar($values)) {
                continue;
            }
            if (!in_array($operator, Filter::acceptedOperator())) {
                continue;
            }
            $value = $this->parseValue($values);
            $filtersObjects[] = new Filter($filterKey, $operator, $value);
        }
        return $filtersObjects;
    }

    private function parseValue($value)
    {
        $val = explode(self::FILTER_VALUES_SEPARATOR, $value);
        if (count($val) == 1) {
            return $value;
        }
        return $val;
    }

}