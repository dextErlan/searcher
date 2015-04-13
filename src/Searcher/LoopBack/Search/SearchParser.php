<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 03.04.15
 * Time: 13:25
 */

namespace Searcher\LoopBack\Search;


use Searcher\QueryParser\Search\Search;

class SearchParser
{

    private $query;

    public function __construct($query)
    {

        $this->query = $query;
    }

    public function get()
    {
        return new Search($this->query);
    }
}