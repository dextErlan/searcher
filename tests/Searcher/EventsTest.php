<?php

namespace Searcher;


use Searcher\Events\ConditionEvent;
use Searcher\Events\FieldEvent;
use Searcher\Events\GroupEvent;
use Searcher\Events\LimitEvent;
use Searcher\Events\OffsetEvent;
use Searcher\Events\OperatorEvent;
use Searcher\Events\OrderEvent;
use Searcher\LoopBack\Parser\Builder;
use Searcher\LoopBack\Parser\Filter\Condition\ConditionInterface;
use Searcher\LoopBack\Parser\Filter\Condition\Exception\InvalidConditionException;
use Searcher\LoopBack\Parser\Filter\FilterCondition;
use Searcher\LoopBack\Parser\Filter\FilterConditionBuilder;
use Searcher\LoopBack\Parser\Order\Order;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventsTest extends \PHPUnit_Framework_TestCase
{

    public function testEventFilters()
    {
        $inputData = array(
            'where' => array(
                'field' => 1,
                'field2' => 2,
                'field3' => array(
                    'field3' => 3,
                    'field4' => 4,
                ),
                'field3a' => array(3, 4),
                'and' => array(
                    'field2' => 123,
                    'field3' => 321,
                    'Lt' => array(
                        'field5' => array('asd', 1, 2, 3),
                        'field6' => 1,
                        'field7' => 8,
                    ),
                    'Gt' => array(
                        'field8' => array('asd', 1, 2, 3),
                        'field9' => 3,
                        'field10' => 'ololo',
                    ),
                ),
                'or' => array(
                    'field2' => 000,
                    'field3' => 111,
                    'Gt' => array(
                        'field8' => array('asd', 1, 2, 3),
                        'field9' => 3,
                        'field10' => 'ololo',
                    ),
                    'nEq' => array(
                        'field11' => array('asd', 1, 2, 3),
                        'field12' => 'asd',
                    ),
                    'like' => array('field14' => 'OlOlo')
                ),
                'some_piece_of_shit' =>
                    array(
                        'field4' => 123
                    )
            ),
            'limit' => 100520,
            'skip' => 45,
            'order' => array(
                'field100' => 'AsC',
                'field200' => 'DeSc',
                'field300' => 'ololo',
            ),
            'some_crap' => array(
                'asd' => array('dsfkjldflkjdf' => 1133),
                'qwe' => 1133,
                'zzzz' => 'dlcvlkj',
            )
        );

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(
            GroupEvent::EVENT_NAME,
            function (GroupEvent $event) {
                if ($event->getGroupName() === FilterCondition::CONDITION_AND) {
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
                if ($event->getField() === 'field100') {
                    throw new InvalidConditionException();
                };
            }
        );

        $eventDispatcher->addListener(
            FieldEvent::EVENT_NAME,
            function (FieldEvent $event) {
                if (!in_array($event->getField(), array('field200', 'field3', 'field15'))) {
                    throw new InvalidConditionException();
                };
            }
        );

        $eventDispatcher->addListener(
            ConditionEvent::EVENT_NAME,
            function (ConditionEvent $event) {
                $condition = $event->getCondition();
                if ($condition->getOperator() === FilterCondition::CONDITION_LIKE) {
                    $value = mb_strtolower($condition->getValue());
                    $condition->setValue($value);
                }
            }
        );

        $builder = new Builder($eventDispatcher);
        $builder->build($inputData);
        $this->assertCount(1, $builder->getFilters());
        $filters = $builder->getFilters();
        $this->assertEquals('or', $filters[0]->getGroup());

        /* @var $expected ConditionInterface[] */
        $expected = array(
            FilterConditionBuilder::create('eq', 'field3', 111, $eventDispatcher),
            FilterConditionBuilder::create('like', 'field15', 'ololo', $eventDispatcher)
        );

        foreach ($filters[0]->getConditions() as $key => $condition) {
            $this->assertEquals($expected[$key]->getValue(), $condition->getValue());
            $this->assertEquals($expected[$key]->getField(), $condition->getField());
            $this->assertEquals($expected[$key]->getOperator(), $condition->getOperator());
        }

        $this->assertEquals(12345, $builder->getLimit());
        $this->assertEquals(54321, $builder->getOffset());
        $this->assertEquals(array(new Order('field200', 'desc')), $builder->getOrders());

    }

    public function testSwitchParameters()
    {
        $input = array(
            'where' => array(
                'foo' => 'bar'
            )
        );

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(
            OperatorEvent::EVENT_NAME,
            function (OperatorEvent $event) {
                if ($event->getField() === 'foo') {
                    $event->setField('pew-pew');
                    if ($event->getValue() === 'bar') {
                        $value = [1, 2, 3, 4];
                    } else {
                        $value = [5, 6, 7, 8];
                    }
                    $event->setValue($value);
                }
            }
        );

        $builder = new Builder($eventDispatcher);
        $builder->build($input);

        $this->assertCount(1, $builder->getFilters());
        $filters = $builder->getFilters();
        $this->assertEquals('and', $filters[0]->getGroup());

        /* @var $expected ConditionInterface[] */
        $expected = array(
            FilterConditionBuilder::create(FilterCondition::CONDITION_IN, 'pew-pew', [1, 2, 3, 4], $eventDispatcher),
        );

        foreach ($filters[0]->getConditions() as $key => $condition) {
            $this->assertEquals($expected[$key]->getValue(), $condition->getValue());
            $this->assertEquals($expected[$key]->getField(), $condition->getField());
            $this->assertEquals($expected[$key]->getOperator(), $condition->getOperator());
        }
    }
}