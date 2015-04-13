<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 13.04.15
 * Time: 19:13
 */

namespace Searcher\QueryParser\Search;


class Search implements SearchInterface
{
    /**
     * @var null
     */
    private $term = null;

    /**
     * @param null $term
     */
    public function __construct($term = null)
    {
        if (is_scalar($term)) {
            $this->term = $term;
        }
    }

    /**
     * @return null
     */
    public function getTerm()
    {
        return $this->term;
    }

}