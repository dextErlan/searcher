<?php

namespace Tests\Searcher;


use PHPUnit\Framework\TestCase;
use Searcher\LoopBack\Parser\Builder;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\EqCondition;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\GtCondition;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\InqCondition;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\LikeCondition;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\LtCondition;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\NeqCondition;
use Searcher\LoopBack\Parser\Filter\FilterCondition;
use Searcher\LoopBack\Parser\Filter\FilterGroupBuilder;
use Searcher\LoopBack\Parser\Filter\FilterGroupConditionBuilder;
use Searcher\LoopBack\Parser\Order\Order;

class ConditionBuilderTest extends TestCase
{
    public function testSimpleCondition()
    {
        $condition = [
            'field' => 1,
            'field2' => 2,
            'field3' => [
                'field3' => 3,
                'field4' => 4,
            ],
            'field3a' => [3, 4],
            'field5' =>['Lt' => ['asd', 1, 2, 3]],
            'field6' => ['Lt' => 1],
            'field7' => ['Lt' => 8],
            'field8' => ['Gt' => ['asd', 1, 2, 3]],
            'field9' => ['Gt' => 3],
            'field10' => ['Gt' => 'ololo'],
            'field11' => ['nEq' => ['asd', 1, 2, 3]],
            'field12' => ['nEq' => 'asd'],
            'field15' => ['LiKe' => 'asd'],
        ];

        $conditionBuilder = new FilterGroupConditionBuilder();
        $conditionBuilder->setConditions($condition);
        $conditions = $conditionBuilder->build();

        $expect = [
            EqCondition::create('field', 1),
            EqCondition::create('field2', 2),
            InqCondition::create('field3a', [3, 4]),
            LtCondition::create('field6', 1),
            LtCondition::create('field7', 8),
            GtCondition::create('field9', 3),
            GtCondition::create('field10', 'ololo'),
            NeqCondition::create('field12', 'asd'),
            LikeCondition::create('field15', 'asd'),
        ];
        $this->assertEquals($expect, $conditions->getConditions());
        $this->assertEquals(FilterCondition::CONDITION_AND, $conditionBuilder->getGroup());
    }

    public function testWhere()
    {
        $condition = [
            'field1' => 123,
            'and' => [
                'field2' => 123,
                'field3' => 321,
            ],
            'or' => [
                'field2' => 000,
                'field3' => 111,
            ],
            'some_piece_of_shit' => [
                'field4' => 123,
            ],
        ];

        $groupCondition = new FilterGroupBuilder();
        $groupObject = $groupCondition->build($condition);

        $expect = [
            FilterGroupConditionBuilder::create(
                'and',
                ['field1' => 123]
            ),
            FilterGroupConditionBuilder::create(
                'and',
                [
                    'field2' => 123,
                    'field3' => 321,
                ]
            ),
            FilterGroupConditionBuilder::create(
                'or',
                [
                    'field2' => 000,
                    'field3' => 111,
                ]
            ),
        ];

        $this->assertEquals($expect, $groupObject);

    }

    public function testComplex()
    {
        $inputData = [
            'where' => [
                'field' => 1,
                'field2' => 2,
                'field3' => [
                    'field3' => 3,
                    'field4' => 4,
                ],
                'field3a' => [3, 4],
                'and' => [
                    'field2' => 123,
                    'field3' => 321,

                    'field5' => ['Lt' => ['asd', 1, 2, 3]],
                    'field6' => ['Lt' => 1],
                    'field7' => ['Lt' => 8],

                    'field8' => ['Gt' => ['asd', 1, 2, 3]],
                    'field9' => ['Gt' => 3],
                    'field10' => ['Gt' => 'ololo'],
                ],
                'or' => [
                    'field2' => 000,
                    'field3' => 111,

                    'field8' => ['Gt' => ['asd', 1, 2, 3]],
                    'field9' => ['Gt' => 3],
                    'field10' => ['Gt' => 'ololo'],

                    'field11' => ['nEq' => ['asd', 1, 2, 3]],
                    'field12' => ['nEq' => 'asd'],
                ],
                'like' => [
                    'field15' => 'ololo',
                    'field16' => 'pewpew',
                ],
                'some_piece_of_shit' =>
                    [
                        'field4' => 123,
                    ],
            ],
            'limit' => 100520,
            'skip' => 45,
            'order' => [
                'field100' => 'AsC',
                'field200' => 'DeSc',
                'field300' => 'ololo',
            ],
            'some_crap' => [
                'asd' => ['dsfkjldflkjdf' => 1133],
                'qwe' => 1133,
                'zzzz' => 'dlcvlkj',
            ],
        ];

        $builder = new Builder();
        $builder->build($inputData);
        $this->assertEquals(45, $builder->getOffset());
        $this->assertEquals(100520, $builder->getLimit());

        $expectOrders = [
            new Order('field100', 'asc'),
            new Order('field200', 'desc'),
        ];

        $this->assertEquals($expectOrders, $builder->getOrders());

        /* @var $expectFilters FilterGroupConditionBuilder[] */
        $expectFilters = [
            FilterGroupConditionBuilder::create('and', ['field' => 1]),
            FilterGroupConditionBuilder::create('and', ['field2' => 2]),
            FilterGroupConditionBuilder::create('and', ['field3a' => [3, 4]]),
            FilterGroupConditionBuilder::create(
                'and',
                [
                    'field2' => 123,
                    'field3' => 321,
                    'field6' => ['lt' => 1],
                    'field7' => ['lt' => 8],
                    'field9' => ['gt' => 3],
                    'field10' => ['gt' => 'ololo'],
                ]
            ),
            FilterGroupConditionBuilder::create(
                'or',
                [
                    'field2' => 000,
                    'field3' => 111,
                    'field9' => ['gt' => 3],
                    'field10' => ['gt' => 'ololo'],
                    'field11' => ['neq' => ['asd', 1, 2, 3]],
                    'field12' => ['neq' => 'asd'],
                ]
            ),
            FilterGroupConditionBuilder::create(
                'and',
                [
                    'field15' => ['like' => 'ololo'],
                    'field16' => ['like' => 'pewpew'],
                ]
            ),
        ];

        foreach ($builder->getFilters() as $key => $builder) {
            $this->assertEquals($expectFilters[$key]->getGroup(), $builder->getGroup());
            $this->assertEquals($expectFilters[$key]->getConditions(), $builder->getConditions());
        }
    }

    public function testOrFilters()
    {
        $inputData = json_decode('{"where":{"or":[{"field1":1},{"field2":"asd"}]},"limit":2,"skip":0}', 1);
        $builder = new Builder();
        $arrayFilters = $builder->build($inputData)->getFilters();
        $this->assertEmpty($arrayFilters);
    }

    public function testFieldNamedAsLikeFilters()
    {
        $inputData = json_decode('{"where":{"like":1}}', 1);
        $builder = new Builder();
        $arrayFilters = $builder->build($inputData)->getFilters();
        $this->assertEquals($arrayFilters[0]->getConditions(), [EqCondition::create('like', 1)]);

        $inputData = json_decode('{"where":{"like":{"like":1}}}', 1);
        $builder = new Builder();
        $arrayFilters = $builder->build($inputData)->getFilters();
        $this->assertEquals($arrayFilters[0]->getConditions(), [LikeCondition::create('like', 1)]);

        $inputData = json_decode('{"where":{"like":{"like":1}}}', 1);
        $builder = new Builder();
        $arrayFilters = $builder->build($inputData)->getFilters();
        $this->assertEquals($arrayFilters[0]->getConditions(), [LikeCondition::create('like', 1)]);
    }
}