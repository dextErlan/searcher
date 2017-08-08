<?php
/**
 * This file is part of the @package@.
 *
 * @author: Nikolay Ermin <nikolay.ermin@sperasoft.com>
 * @version: @version@
 */


namespace Searcher\LoopBack\Parser\Filter;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface FilterInterface
{
    public static function create($groups, EventDispatcherInterface $dispatcher = null);
}
