<?php

namespace Searcher\Events;


use Searcher\LoopBack\Parser\Builder;
use Searcher\LoopBack\Parser\Filter\Condition\CompareCondition\EqCondition;
use Searcher\LoopBack\Parser\Filter\FilterCondition;
use Searcher\LoopBack\Parser\Filter\FilterGroupConditionBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PostConditionSearchTest extends \PHPUnit_Framework_TestCase
{

    private function getInputData()
    {
        return [
            'where' => [
                'AND' => [
                    'field1' => 1,
                    'field2' => 2,
                ],
            ],
        ];
    }

    public function testPostConditionEventCanChangeFieldName()
    {

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(
            EventNames::CONDITION_POST_POPULATE_EVENT,
            function (ConditionEvent $event) {
                if ($event->getCondition()->getField() === 'field1') {
                    $event->getCondition()->setField('field2');
                }
            }
        );

        $builder = new Builder($eventDispatcher);
        $builder->build($this->getInputData());
        $filters = $builder->getFilters();


        // expect only one group
        $this->assertCount(1, $filters);
        // and this group is 'AND'
        $firstGroup = $filters[0];
        $this->assertEquals(FilterCondition::CONDITION_AND, $firstGroup->getGroup());
        // group contains 2 conditions
        $conditions = $firstGroup->getConditions();
        $this->assertCount(2, $conditions);
        // First condition is EQ conditions
        $firstCondition = $conditions[0];
        $this->assertInstanceOf(EqCondition::class, $firstCondition);
        // First condition And contains field2 as field and 1 as value and eq as operator
        $this->assertEquals(
            ['field2', 1, FilterCondition::CONDITION_EQ],
            [$firstCondition->getField(), $firstCondition->getValue(), $firstCondition->getOperator()]
        );
    }

    public function testPostConditionEventCanChangeValue()
    {

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(
            EventNames::CONDITION_POST_POPULATE_EVENT,
            function (ConditionEvent $event) {
                if ($event->getCondition()->getValue() === 2) {
                    $event->getCondition()->setValue(123);
                }
            }
        );

        $builder = new Builder($eventDispatcher);
        $builder->build($this->getInputData());
        $filters = $builder->getFilters();


        $firstGroup = $filters[0];
        $conditions = $firstGroup->getConditions();
        $secondCondition = $conditions[1];
        $this->assertInstanceOf(EqCondition::class, $secondCondition);
        // First condition And contains field2 as field and 1 as value and eq as operator
        $this->assertEquals(
            ['field2', 123, FilterCondition::CONDITION_EQ],
            [$secondCondition->getField(), $secondCondition->getValue(), $secondCondition->getOperator()]
        );
    }
}