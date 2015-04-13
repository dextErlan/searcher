<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 13.04.15
 * Time: 18:58
 */

namespace Searcher\LoopBack\Page;


use Searcher\ArrayUtils;
use Searcher\QueryParser\Page\Page;

class PageParser
{
    private $page;

    const PARAM_LIMIT = 'limit';
    const PARAM_OFFSET = 'offset';

    /**
     * @param $page
     */
    public function __construct($page)
    {

        $this->page = $page;
    }

    public function get()
    {
        $limit = ArrayUtils::get($this->page, self::PARAM_LIMIT, Page::DEFAULT_MAX);
        $offset = ArrayUtils::get($this->page, self::PARAM_OFFSET, 0);
        return new Page($limit, $offset);
    }
}