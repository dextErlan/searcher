<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 16.04.15
 * Time: 18:36
 */

namespace Searcher\LoopBack\Parser\Pagination;


class PaginationBuilder
{

    const CONDITION_ASC = 'asc';
    const CONDITION_DESC = 'desc';

    public function __construct($field,$condition)
    {

    }

    private function getAvailable(){
        return array();
    }
}