# Search syntax guide (Searcher lib)

1. What is searcher?
2. Work principle
3. Syntax
4. Transformers
5. Example queries
6. Extensions

## 1. What is searcher?
Searcher is a library for parsing of structured queries. Query syntax is similar to the Loopback library ()

Features:

 - working with formalized syntax
 - fault tolerance (incorrect syntax, values and fields will be ignored on parsing without any exception)
 - ability to extend query parsing with listeners

## 2. Work principle

The library takes as structured data in the form of an array and outputs it in a strictly structured object that you can convert to a DB query
(Sql, NoSql, ElasticSearch, etc.) through the transformer.
In the library there is a transformer which transform the query into a syntax suitable for the request through the `php-elasticsearch` library .

Library is a set of following components:

1. Builder – class, which converts input data to strictly structured query object
2. TransformerInterface – interface for converting output query to a data storage query.
(at the moment `ElasticSearchTransformer` is the only implementation for `php-elasticsearch` support)
3. Event – events for flexible management of query building.

## 3. Syntax

Syntax of the structured query looks like: `filters & limit & offset & sort`

### `Filter` syntax:
[where][group][condition][field] = value

description:

  where -  marker word used in case there are filters in this group
  group -  logic group of requests, can be OR & AND, this is optional parameter, by default it’s AND.
  This group was introduced to get requests like: `Need to get data where field1=X OR field2=Y`

condition – relation between field and value. Can be:

    EQ – means that field should be equal to value (field=value)
    NEQ - means that  field should not be equal to value (field!=value)
    GT - means that field should be more than value (field > value), correct results are numerics only
    GTE -  means that field should be more or equal than value (field >= value), correct results are numerics only

    LT - means that field should be less than value (field < value), correct values are numerics only
    LTE - means that field should be less or equal to the value (field <= value), correct values are numerics only
    INQ -  means that fie  ld must contain at least 1 value from the list (field ∈ N , N = {X,Y, Z ….. } )
    NIN -  means that field must not contain any value from the list (field ∉ N , N = {X,Y, Z ….. } )
    LIKE -  means that field must be like value

Condition parameter is optional, by default it’s EQ

### `Limit` & `Offset` syntax

offset – how many results should be ignored at first (integer)
limit -  how many results bring to output (integer)

### `Sort` syntax:

[sort][field] = condition

description:

- sort – marker word means that control area has sorting
- field -  field according to which sorting must be done
- condition – sort direction, can be ASC (straight sorting) или DESC (inverse sorting)

Some filter piculiarities:

1. `[group]` and `[condition]` values are not mandatory, if they are not present in request, group gets AND, condition EQ
2. In case of condition EQ, and value is a list, condition changes to INQ, this is done to create brief filters as [where][field] = [X, Y, Z, ...]
3. Condition's GT, GTE, LT, LTE demand numeric results, otherwise these conditions are not taken into consideration.

### 4. Tranformers

Tranformers – objects, which transforms output query to a understandable query for data storage.

Transformer must implement TransformerInterface.

### 5. Example queries (JSON syntax):
Find first `10` entities starting from `2nd`, where `field == value` and sort it on `field2` ascending:

```
{
"where" : {
      "field":"value"
     },
"limit":10,
"skip":2,
"sort" : {"field2":"asc"}
}
```

Find all entities where `field` contains at least one of the values in  `[ "value1", "value2", "value3" ]`, and sort it on `field2` ascending and `field3` descending:

```
{
"where" : {
      "field": [ "value1", "value2", "value3" ]
     },
"sort" : { "field2":"asc", "field3":"desc" }
}
```

Find all entities where `field < 10 && field2 < 20 && field3 >= 5`:

```
{
"where" : {
      "LT":{"field": 10,"field2":20},
      "GTE":{"field3": 5}
     }
}
```

Find all entities where ` field3 >= 5 &&  ( field < 10 || field2 < 20 )`:

```
{
    "where" : {
        "GTE":{"field3": 5},
        "OR: {
            "LT":{"field": 10,"field2":20},
        }
    }
}
```

Equivalent:

```
{
    "where" : {
        "AND": {
            "GTE":{"field3": 5}
        },
        "OR: {
            "LT":{"field": 10,"field2":20},
        }
    }
}
```

## 6. Extensions

The library has the ability to extend it with event listeners which can:

1. filter values
2. filter field
3. filter groups
4. Change `skip` and `limit` values
5. Change `condition`, `field` and `value` values

Event system is based on `symfony/event-dispatcher`

Available events:

 - ConditionEvent - allows you to check and change the state AFTER processing
 - FieldEvent – allows you to check and change certain field value
 - GroupEvent -  allows you to check group value
 - LimitEvent - allows you to check and change `limit` AFTER processing
 - OffsetEvent - allows you to check and change `offset` AFTER processing
 - OperatorEvent -  allows you to check and change the state BEFORE processing
 - OrderEvent - allows you to check fields and sort order

If EventListener throws `InvalidConditionException`, library will stop current state/sort/group/field checking and
state/sort/group/field will be excluded from processing.

IMPORTANT: EventListeners will only work if you explicitly send EventDispatcher to a Builder constructor.

#### 6.1 EventListener usage examples:

If condition is like make value lowercase.

```php
$eventDispatcher->addListener(
            ConditionEvent::EVENT_NAME,
            function (ConditionEvent $event) {
                $condition = $event->getCondition();
                if ($condition->getOperator() == FilterCondition::CONDITION_LIKE) {
                    $value = mb_strtolower($condition->getValue());
                    $condition->setValue($value);
                }
            }
        );
```

Do not process field with name `field100`:

```php
        $eventDispatcher->addListener(
            OrderEvent::EVENT_NAME,
            function (OrderEvent $event) {
                if ($event->getField() == "field100") {
                    throw new InvalidConditionException();
                };
            }
        );
```


General example:

```php
$inputQuery = array(
     "where" => array(
             "or" => array( "field" => "value"),
             "and" => array( "field2" => "value"),
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
$builder = new Builder($inputQuery, $eventDispatcher);
$builder->build();
$esBuilder = new ElasticSearchTransformer($builder->build());
$result = $esBuilder->build()->transform();
```

In this example we will receive a request suitable for `php-elasticsearch` library and we excluded `AND` group  from processing.