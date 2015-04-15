<?php

use Searcher\LoopBack\LoopbackQueryParser;

class SimpleTest extends \PHPUnit_Framework_TestCase
{

//    public function testOne()
//    {
//        $a = new OrderParser('+asd,-qwe,dfjdfjhd');
//        $results = $a->get();
//        foreach ($results as $result) {
//            $this->assertInstanceOf(\Searcher\QueryParser\Order\OrderInterface::class, $result);
//        }
//        $this->assertEquals(\Searcher\QueryParser\Order\Order::DIRECTION_ASC,$results[0]->getDirection());
//        $this->assertEquals(\Searcher\QueryParser\Order\Order::DIRECTION_DESC,$results[1]->getDirection());
//        $this->assertEquals(\Searcher\QueryParser\Order\Order::DIRECTION_ASC,$results[2]->getDirection());
//        $this->assertEquals('asd',$results[0]->getField());
//        $this->assertEquals('qwe',$results[1]->getField());
//        $this->assertEquals('dfjdfjhd',$results[2]->getField());
//    }

    public function testComplex()
    {
        $string = 'filter[where][asd]=123&filter[where][qwe][lt]=123&filter[where][asdd]=432,123,456&filter[where][asd1][lt]=321&filter[where][asd1][gt]=123';
        $string .= "&q=asdasdasd&order=+asd,-qwe&page[limit]=53&page[offset]=10";
        $queryArray = array();
        parse_str($string, $queryArray);
        $queryParser = new LoopbackQueryParser($queryArray);
        $expected =
            array(
                new \Searcher\QueryParser\Filter\Filter(
                    "asd",
                    \Searcher\QueryParser\Filter\Filter::OPERATOR_EQ,
                    123
                ),
                new \Searcher\QueryParser\Filter\Filter(
                    "qwe",
                    \Searcher\QueryParser\Filter\Filter::OPERATOR_LT,
                    123
                ),
                new \Searcher\QueryParser\Filter\Filter(
                    "asdd",
                    \Searcher\QueryParser\Filter\Filter::OPERATOR_EQ,
                    array(432, 123, 456)
                ),
                new \Searcher\QueryParser\Filter\Filter(
                    "asd1",
                    \Searcher\QueryParser\Filter\Filter::OPERATOR_LT,
                    321
                ),
                new \Searcher\QueryParser\Filter\Filter(
                    "asd1",
                    \Searcher\QueryParser\Filter\Filter::OPERATOR_GT,
                    123
                ),
            );
        $this->assertEquals($expected, $queryParser->getFilters());
        $this->assertEquals("asdasdasd", $queryParser->getSearch()->getTerm());

        $expectedOrder = array(
            new \Searcher\QueryParser\Order\Order('asd', \Searcher\QueryParser\Order\Order::DIRECTION_ASC),
            new \Searcher\QueryParser\Order\Order('qwe', \Searcher\QueryParser\Order\Order::DIRECTION_DESC)
        );
        $this->assertEquals($expectedOrder, $queryParser->getOrder());

        $esParser = new \Searcher\Transformer\ElasticSearchTransformer($queryParser);

        $esExpected = array(
            "query" => array(
                "filtered" => array(
                    "match" => array(
                        "_all" => "asdasdasd"
                    ),
                    "filter" => array(
                        array(
                            "term" => array("asd" => 123)
                        ),
                        array(
                            "range" => array(
                                "qwe" => array(

                                    "lt" => 123
                                )
                            )
                        ),
                        array(
                            "terms" => array(
                                "asdd" => array(
                                    432,
                                    123,
                                    456
                                )
                            )
                        ),
                        array(
                            "range" => array(
                                "asd1" => array(
                                    "lt" => 321
                                )
                            )

                        ),
                        array(
                            "range" => array(
                                "asd1" => array(
                                    "gt" => 123
                                )
                            )

                        )
                    )
                )
            ),
            "sort" => array(
                array(
                    "asd" => array(
                        "order" => "ASC"
                    )
                ),
                array(
                    "qwe" => array
                    (
                        "order" => "DESC"
                    )

                )
            ),
            "from" => 10,
            "size" => 53
        );

        $this->assertEquals($esExpected, $esParser->getQuery());
    }
}