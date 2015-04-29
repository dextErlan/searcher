<?php

namespace Searcher;


use Searcher\Events\FieldEvent;
use Searcher\Events\GroupEvent;
use Searcher\Events\LimitEvent;
use Searcher\Events\OffsetEvent;
use Searcher\Events\OrderEvent;
use Searcher\LoopBack\Parser\Builder;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\EqCondition;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\GtCondition;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\InqCondition;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\LikeCondition;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\LtCondition;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\NeqCondition;
use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\LoopBack\Parser\Filter\FilterCondition;
use Searcher\LoopBack\Parser\Filter\FilterConditionBuilder;
use Searcher\LoopBack\Parser\Filter\FilterGroupBuilder;
use Searcher\LoopBack\Parser\Filter\FilterGroupConditionBuilder;
use Searcher\LoopBack\Parser\Order\Order;
use Searcher\Transformer\ElasticSearchTransformer;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ConditionBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleCondition()
    {
        $condition = array(
            "field" => 1,
            "field2" => 2,
            "field3" => array(
                "field3" => 3,
                "field4" => 4,
            ),
            "field3a" => array(3, 4),
            "Lt" => array(
                "field5" => array("asd", 1, 2, 3),
                "field6" => 1,
                "field7" => 8,
            ),
            "Gt" => array(
                "field8" => array("asd", 1, 2, 3),
                "field9" => 3,
                "field10" => "ololo",
            ),
            "nEq" => array(
                "field11" => array("asd", 1, 2, 3),
                "field12" => "asd",
            ),
            "LiKe" => array(
                "field15" => "asd",
            )
        );

        $conditionBuilder = new FilterGroupConditionBuilder();
        $conditionBuilder->setConditions($condition);
        $conditions = $conditionBuilder->build();

        $expect = array(
            EqCondition::create("field", 1),
            EqCondition::create("field2", 2),
            InqCondition::create("field3a", array(3, 4)),
            LtCondition::create("field6", 1),
            LtCondition::create("field7", 8),
            GtCondition::create("field9", 3),
            NeqCondition::create("field12", "asd"),
            LikeCondition::create("field15", "asd"),
        );
        $this->assertEquals($expect, $conditions->getConditions());
        $this->assertEquals(FilterCondition::CONDITION_AND, $conditionBuilder->getGroup());
    }

    public function testWhere()
    {
        $condition = array(
            "field1" => 123,
            "and" => array(
                "field2" => 123,
                "field3" => 321
            ),
            "or" => array(
                "field2" => 000,
                "field3" => 111
            ),
            "some_piece_of_shit" =>
                array(
                    "field4" => 123
                )
        );

        $groupCondition = new FilterGroupBuilder();
        $groupCondition->setGroupCondition($condition);
        $groupObject = $groupCondition->build();

        $expect = array(
            FilterGroupConditionBuilder::create(
                "and",
                array("field1" => 123)
            ),
            FilterGroupConditionBuilder::create(
                "and",
                array(
                    "field2" => 123,
                    "field3" => 321
                )
            ),
            FilterGroupConditionBuilder::create(
                "or",
                array(
                    "field2" => 000,
                    "field3" => 111
                )
            )
        );

        $this->assertEquals($expect, $groupObject);

    }


    public function testComplex()
    {
        $inputData = array(
            "where" => array(
                "field" => 1,
                "field2" => 2,
                "field3" => array(
                    "field3" => 3,
                    "field4" => 4,
                ),
                "field3a" => array(3, 4),
                "and" => array(
                    "field2" => 123,
                    "field3" => 321,
                    "Lt" => array(
                        "field5" => array("asd", 1, 2, 3),
                        "field6" => 1,
                        "field7" => 8,
                    ),
                    "Gt" => array(
                        "field8" => array("asd", 1, 2, 3),
                        "field9" => 3,
                        "field10" => "ololo",
                    ),
                ),
                "or" => array(
                    "field2" => 000,
                    "field3" => 111,
                    "Gt" => array(
                        "field8" => array("asd", 1, 2, 3),
                        "field9" => 3,
                        "field10" => "ololo",
                    ),
                    "nEq" => array(
                        "field11" => array("asd", 1, 2, 3),
                        "field12" => "asd",
                    )
                ),
                "like" => array(
                    "field15" => "ololo",
                    "field16" => "pewpew",
                ),
                "some_piece_of_shit" =>
                    array(
                        "field4" => 123
                    )
            ),
            "limit" => 100520,
            "skip" => 45,
            "order" => array(
                "field100" => "AsC",
                "field200" => "DeSc",
                "field300" => "ololo",
            ),
            "some_crap" => array(
                "asd" => array("dsfkjldflkjdf" => 1133),
                "qwe" => 1133,
                "zzzz" => "dlcvlkj",
            )
        );

        $builder = new Builder($inputData);
        $builder->build();
        $this->assertEquals(45, $builder->getOffset());
        $this->assertEquals(100520, $builder->getLimit());

        $expectOrders = array(
            new Order("field100", "asc"),
            new Order("field200", "desc"),
        );

        $this->assertEquals($expectOrders, $builder->getOrders());

        /* @var $expectFilters FilterGroupConditionBuilder[] */
        $expectFilters = array(
            FilterGroupConditionBuilder::create("and", array("field" => 1)),
            FilterGroupConditionBuilder::create("and", array("field2" => 2)),
            FilterGroupConditionBuilder::create("and", array("field3a" => array(3, 4))),
            FilterGroupConditionBuilder::create(
                "and",
                array(
                    "field2" => 123,
                    "field3" => 321,
                    "lt" => array(
                        "field6" => 1,
                        "field7" => 8,
                    ),
                    "gt" => array(
                        "field9" => 3,
                    ),
                )
            ),
            FilterGroupConditionBuilder::create(
                "or",
                array(
                    "field2" => 000,
                    "field3" => 111,
                    "gt" => array(
                        "field9" => 3,
                    ),
                    "neq" => array(
                        "field11" => array("asd", 1, 2, 3),
                        "field12" => "asd",
                    ),
                )
            ),
            FilterGroupConditionBuilder::create(
                "and",
                array(
                    "like" => array(
                        "field15" => "ololo",
                        "field16" => "pewpew",
                    )
                )
            ),
        );

        foreach ($builder->getFilters() as $key => $builder) {
            $this->assertEquals($expectFilters[$key]->getGroup(), $builder->getGroup());
            $this->assertEquals($expectFilters[$key]->getConditions(), $builder->getConditions());
        }
    }

    public function testOrFilters()
    {
        $inputData = json_decode('{"where":{"or":[{"field1":1},{"field2":"asd"}]},"limit":2,"skip":0}', 1);
        $builder = new Builder($inputData);
        $arrayFilters = $builder->build()->getFilters();
        $this->assertEmpty($arrayFilters);
    }

    public function testEventFilters()
    {
        $inputData = array(
            "where" => array(
                "field" => 1,
                "field2" => 2,
                "field3" => array(
                    "field3" => 3,
                    "field4" => 4,
                ),
                "field3a" => array(3, 4),
                "and" => array(
                    "field2" => 123,
                    "field3" => 321,
                    "Lt" => array(
                        "field5" => array("asd", 1, 2, 3),
                        "field6" => 1,
                        "field7" => 8,
                    ),
                    "Gt" => array(
                        "field8" => array("asd", 1, 2, 3),
                        "field9" => 3,
                        "field10" => "ololo",
                    ),
                ),
                "or" => array(
                    "field2" => 000,
                    "field3" => 111,
                    "Gt" => array(
                        "field8" => array("asd", 1, 2, 3),
                        "field9" => 3,
                        "field10" => "ololo",
                    ),
                    "nEq" => array(
                        "field11" => array("asd", 1, 2, 3),
                        "field12" => "asd",
                    )
                ),
                "some_piece_of_shit" =>
                    array(
                        "field4" => 123
                    )
            ),
            "limit" => 100520,
            "skip" => 45,
            "order" => array(
                "field100" => "AsC",
                "field200" => "DeSc",
                "field300" => "ololo",
            ),
            "some_crap" => array(
                "asd" => array("dsfkjldflkjdf" => 1133),
                "qwe" => 1133,
                "zzzz" => "dlcvlkj",
            )
        );

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(
            GroupEvent::EVENT_NAME,
            function (GroupEvent $event) {
                if ($event->getGroupName() == "and") {
                    throw new InvalidConditionException();
                }
            }
        );

        $eventDispatcher->addListener(
            LimitEvent::EVENT_NAME,
            function (LimitEvent $event) {
                $event->getLimitBuilder()->setLimit(12345);
            }
        );

        $eventDispatcher->addListener(
            OffsetEvent::EVENT_NAME,
            function (OffsetEvent $event) {
                $event->getOffsetBuilder()->setOffset(54321);
            }
        );

        $eventDispatcher->addListener(
            OrderEvent::EVENT_NAME,
            function (OrderEvent $event) {
                if ($event->getField() == "field100") {
                    throw new InvalidConditionException();
                };
            }
        );

        $eventDispatcher->addListener(
            FieldEvent::EVENT_NAME,
            function (FieldEvent $event) {
                if (!in_array($event->getField(), array("field200", "field3"))) {
                    throw new InvalidConditionException();
                };
            }
        );

        $builder = new Builder($inputData, $eventDispatcher);
        $builder->build();
        $this->assertCount(1, $builder->getFilters());
        $this->assertEquals("or", $builder->getFilters()[0]->getGroup());
        $this->assertEquals(
            array(FilterConditionBuilder::create("eq", "field3", 111, $eventDispatcher)),
            $builder->getFilters()[0]->getConditions()
        );
        $this->assertEquals(12345, $builder->getLimit());
        $this->assertEquals(54321, $builder->getOffset());
        $this->assertEquals(array(new Order("field200", "desc")), $builder->getOrders());

    }

    public function testElasticTransformer()
    {
        $inputQuery = array(
            "where" => array(
                "nin" => array("user_id" => array(3)),
                "neq" => array("product_id" => "27"),
                "or" => array(
                    "lte" => array(
                        "details.id" => 27
                    ),
                    "gte" => array(
                        "details.id" => 10
                    ),
                    "in" => array("user_id" => array(1)),
                ),
                "like" => array("description" => "My awe")
            )
        );

        $builder = new Builder($inputQuery);
        $esBuilder = new ElasticSearchTransformer($builder);
        $result = $esBuilder->build()->transform();
        $this->assertEquals(array("size" => 25, "from" => 0), $result);


        $expect = array(
            "from" => 0,
            "size" => 25,
            "body" => array(
                "query" => array(
                    "filtered" => array(
                        "filter" => array(
                            "bool" => array(
                                "must_not" => array(
                                    array(
                                        "terms" => array(
                                            "user_id" => array(3),
                                        ),
                                    ),
                                    array(
                                        "term" => array("product_id" => 27),
                                    ),
                                ),
                                "should" => array(
                                    array(
                                        "range" => array(
                                            "details.id" => array("lte" => 27),
                                        ),
                                    ),
                                    array(
                                        "range" => array(
                                            "details.id" => array("gte" => 10),
                                        ),
                                    ),
                                ),
                                "must" => array(
                                    array(
                                        "regexp" => array(
                                            "description" => array(
                                                "value" => "My awe.*",
                                                "max_determinized_states" => 1
                                            )
                                        )
                                    ),
                                )
                            ),
                        ),
                    ),
                ),
            )
        );
        $esBuilder = new ElasticSearchTransformer($builder->build());
        $result = $esBuilder->build()->transform();
        $this->assertEquals($expect, $result);
    }
}