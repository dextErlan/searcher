<?php
/**
 * Created by IntelliJ IDEA.
 * User: unit
 * Date: 16.04.15
 * Time: 11:02
 */

namespace Searcher;


use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\EqCondition;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\GtCondition;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\InqCondition;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\LtCondition;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\NeqCondition;
use Searcher\LoopBack\Parser\Filter\FilterCondition;
use Searcher\LoopBack\Parser\Filter\FilterConditionBuilder;
use Searcher\LoopBack\Parser\Filter\FilterConditionGroup;

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
            "field3a" => array(3,4),
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
            )
        );

        $conditionBuilder = new FilterConditionBuilder("aNd", $condition);

        $expect = array(
            new EqCondition("field", 1),
            new EqCondition("field2", 2),
            new InqCondition("field3a", array(3, 4)),
            new LtCondition("field6", 1),
            new LtCondition("field7", 8),
            new GtCondition("field9", 3),
            new NeqCondition("field12", "asd"),
        );
        $this->assertEquals($expect, $conditionBuilder->getConditions());
        $this->assertEquals(FilterCondition::CONDITION_AND, $conditionBuilder->getGroup());
    }

    public function te1stWhere()
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

        $groupCondition = new FilterConditionGroup($condition);

        $expect = array(
            new FilterConditionBuilder(
                "and",
                array("field1" => 123)
            ),
            new FilterConditionBuilder(
                "and", array(
                    "field2" => 123,
                    "field3" => 321
                )
            ),
            new FilterConditionBuilder(
                "or", array(
                    "field2" => 000,
                    "field3" => 111
                )
            )
        );

        $this->assertEquals($expect, $groupCondition->getConditions());
    }
}