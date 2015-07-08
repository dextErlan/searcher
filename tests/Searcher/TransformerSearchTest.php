<?php
namespace Searcher;


use Searcher\LoopBack\Parser\Builder;
use Searcher\Transformer\ElasticSearchTransformer;

class TransformerSearchTest extends \PHPUnit_Framework_TestCase
{

    public function testElasticTransformer()
    {
        $inputQuery = array(
            'where' => array(
                'nin' => array('user_id' => array(3)),
                'neq' => array('product_id' => '27'),
                'or' => array(
                    'lte' => array(
                        'details.id' => 27
                    ),
                    'gte' => array(
                        'details.id' => 10
                    ),
                    'in' => array('user_id' => array(1)),
                ),
                'like' => array('description' => 'My awe')
            ),
            'order' => array(
                'user_id' => 'asc'
            )
        );

        $builder = new Builder($inputQuery);
        $esBuilder = new ElasticSearchTransformer($builder);
        $result = $esBuilder->build()->transform();
        $this->assertEquals(array('size' => 25, 'from' => 0), $result);


        $expect = array(
            'from' => 0,
            'size' => 25,
            'body' => array(
                'sort' => array(
                    array(
                        'user_id' => 'asc'
                    )
                ),
                'query' => array(
                    'filtered' => array(
                        'filter' => array(
                            'bool' => array(
                                'must_not' => array(
                                    array(
                                        'terms' => array(
                                            'user_id' => array(3),
                                        ),
                                    ),
                                    array(
                                        'term' => array('product_id' => 27),
                                    ),
                                ),
                                'should' => array(
                                    array(
                                        'range' => array(
                                            'details.id' => array('lte' => 27),
                                        ),
                                    ),
                                    array(
                                        'range' => array(
                                            'details.id' => array('gte' => 10),
                                        ),
                                    ),
                                ),
                                'must' => array(
                                    array(
                                        'regexp' => array(
                                            'description' => array(
                                                'value' => 'My awe.*',
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