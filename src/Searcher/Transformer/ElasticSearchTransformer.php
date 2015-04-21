<?php

namespace Searcher\Transformer;

use Searcher\LoopBack\Parser\Builder;
use Searcher\LoopBack\Parser\BuilderInterface;
use Searcher\LoopBack\Parser\Filter\FilterCondition;

class ElasticSearchTransformer implements TransformerInterface, BuilderInterface
{
    const BOOL_MUST = "must";
    const BOOL_MUST_NOT = "must_not";
    const BOOL_SHOULD = "should";
    const BOOL = "bool";
    const TERM = "term";
    const TERMS = "terms";
    /**
     * @var Builder
     */
    private $builder;

    /**
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {

        $this->builder = $builder;
    }

    private $results = array();

    public function build()
    {
        $rangeOperators = array(
            FilterCondition::CONDITION_GT,
            FilterCondition::CONDITION_GTE,
            FilterCondition::CONDITION_LT,
            FilterCondition::CONDITION_LTE,
        );

        $mustNotOperators = array(
            FilterCondition::CONDITION_NIN,
            FilterCondition::CONDITION_NEQ,
        );

        $termsOperators = array(
            FilterCondition::CONDITION_IN,
            FilterCondition::CONDITION_NIN,
        );

        $builder = $this->builder;
        $query = array();
        $query["from"] = $builder->getOffset();
        $query["size"] = $builder->getLimit();

        foreach ($builder->getOrders() as $order) {
            $query["sort"][] = array(
                $order->getField() => $order->getDirection()
            );
        }

        $filters = $builder->getFilters();
        if (!empty($filters)) {
            $query["body"]["query"]["filtered"]["filter"][self::BOOL] = array();
        }

        foreach ($filters as $filter) {
            $groupName = self::BOOL_MUST;
            if ($filter->getGroup() == FilterCondition::CONDITION_OR) {
                $groupName = self::BOOL_SHOULD;
            }
            foreach ($filter->getConditions() as $condition) {

                $conditionArray = array(
                    self::TERM => array(
                        $condition->getField() => $condition->getValue()
                    )
                );

                $operator = $condition->getOperator();
                if (in_array($operator, $mustNotOperators)) {
                    if ($groupName == self::BOOL_MUST) {
                        $groupName = self::BOOL_MUST_NOT;
                    } else {
                        $groupName = self::BOOL_MUST;
                    }
                }

                if (in_array($operator, $rangeOperators)) {
                    $conditionArray = array(
                        "range" => array(
                            $condition->getField() => array(
                                $operator => $condition->getValue()
                            )
                        )
                    );
                }

                if (in_array($operator, $termsOperators)) {
                    $conditionArray = array(
                        self::TERMS => array(
                            $condition->getField() => $condition->getValue()
                        )
                    );
                }
                $query["body"]["query"]["filtered"]["filter"][self::BOOL][$groupName][] = $conditionArray;
            }
        }
        $this->results = $query;

        return $this;
    }

    public function transform()
    {
        return $this->results;
    }
}