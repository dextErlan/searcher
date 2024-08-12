<?php
namespace Tests\Searcher;


use PHPUnit\Framework\TestCase;
use Searcher\LoopBack\Parser\Builder;
use Searcher\Transformer\ElasticSearchTransformer;

class TransformerSearchTest extends TestCase
{

    public function testElasticTransformer()
    {
        $inputQuery = [
            'where' => [
                'user_id' => ['nin' => [3]],
                'product_id' => ['neq' => '27'],
                'or' => [
                    'details.id' => [
                        'lte' => 27,
                        'gte' => 10,
                    ],
                    'user_id' => ['in' => [1]],
                ],
                'description' => ['like' => 'My awe'],
            ],
            'order' => [
                'user_id' => 'asc',
            ],
        ];

        $builder = new Builder();
        $esBuilder = new ElasticSearchTransformer($builder);
        $result = $esBuilder->build()->getResult();
        $this->assertEquals(['size' => 25, 'from' => 0], $result);

        $builder->build($inputQuery);

        $expect = [
            'from' => 0,
            'size' => 25,
            'body' => [
                'sort' => [
                    [
                        'user_id' => 'asc',
                    ],
                ],
                'query' => [
                    'filtered' => [
                        'filter' => [
                            'bool' => [
                                'must_not' => [
                                    [
                                        'terms' => [
                                            'user_id' => [3],
                                        ],
                                    ],
                                    [
                                        'term' => ['product_id' => 27],
                                    ],
                                ],
                                'should' => [
                                    [
                                        'range' => [
                                            'details.id' => ['lte' => 27],
                                        ],
                                    ],
                                    [
                                        'range' => [
                                            'details.id' => ['gte' => 10],
                                        ],
                                    ],
                                ],
                                'must' => [
                                    [
                                        'regexp' => [
                                            'description' => [
                                                'value' => 'My awe.*',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $esBuilder = new ElasticSearchTransformer($builder);
        $result = $esBuilder->build()->getResult();
        $this->assertEquals($expect, $result);
    }
}