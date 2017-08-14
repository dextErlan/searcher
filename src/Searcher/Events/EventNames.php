<?php

namespace Searcher\Events;

/**
 * Class EventNames
 * @package Searcher\Events
 *
 * List of event names
 */
final class EventNames
{
    const CONDITION_PRE_POPULATE_EVENT = 'searcher.condition';
    const CONDITION_POST_POPULATE_EVENT = 'searcher.condition.pre';

    /**
     * Uses before builders build condition,order,limit,etc
     */
    const FIELD_EVENT = 'searcher.field';

    const GROUP_EVENT = 'searcher.group';
    const LIMIT_EVENT = 'searcher.limit';
    const OFFSET_EVENT = 'searcher.offset';
    const OPERATOR_EVENT = 'searcher.operator';
    const ORDER_EVENT = 'searcher.order';

}