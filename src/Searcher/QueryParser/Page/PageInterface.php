<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 13.04.15
 * Time: 18:56
 */

namespace Searcher\QueryParser\Page;


interface PageInterface {

    /**
     * @return int
     */
    public function getLimit();
    /**
     * @return int
     */
    public function getOffset();
}