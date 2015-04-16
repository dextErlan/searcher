<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 15.04.15
 * Time: 18:20
 */

namespace Searcher\LoopBack\Parser\Filter;


class FilterWhere
{
    private $filters;

    /**
     * @param array $filters
     */
    public function __construct($filters)
    {
        if (!is_array($filters)) {
            $filters = array();
        }

        $this->filters = $filters;
    }

    /**
     * @return FilterWhere
     */
    public function getConditionGroups()
    {
        $conditionGroups = array();
        foreach ($this->filters as $filter) {
            $conditionGroups[] = new FilterConditionGroup($filter);
        }
        return $conditionGroups;
    }
}