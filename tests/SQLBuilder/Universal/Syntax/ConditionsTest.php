<?php
use SQLBuilder\Universal\Syntax\Conditions;
use SQLBuilder\Criteria;
use SQLBuilder\ArgumentArray;
use SQLBuilder\DataType\Unknown;

class ConditionsTest extends PHPUnit_Framework_TestCase
{
    public function testAppendExpr() {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;
        ok($driver);

        $exprBuilder = new Conditions;
        $exprBuilder->appendBinExpr('a', '=', 123);
        $sql = $exprBuilder->toSql($driver, $args);
        is("a = 123",$sql);
    }

    public function testInExpr() {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;
        $expr = new Conditions;
        $expr->in('b', [ 'a', 'b', 'c' ]);
        $sql = $expr->toSql($driver, $args);
        is("b IN ('a','b','c')", $sql);
    }


    public function testNotInExpr() {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;
        $expr = new Conditions;
        $expr->notIn('z', [ 'a', 'b', 'c' ]);
        $sql = $expr->toSql($driver, $args);
        is("z NOT IN ('a','b','c')", $sql);
    }

    public function testEqual() {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;
        $expr = new Conditions;
        $expr->equal('a', 1);
        $sql = $expr->toSql($driver, $args);
        is("a = 1", $sql);
    }


    public function testLessThan() {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;
        $expr = new Conditions;
        $expr->lessThan('view', 100);
        $sql = $expr->toSql($driver, $args);
        is("view < 100", $sql);
    }

    public function testLessThanOrEqual() {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;
        $expr = new Conditions;
        $expr->lessThanOrEqual('view', 100);
        $sql = $expr->toSql($driver, $args);
        is("view <= 100", $sql);
    }

    public function testGreaterThan() {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;
        $expr = new Conditions;
        $expr->greaterThan('view', 100);
        $sql = $expr->toSql($driver, $args);
        is("view > 100", $sql);
    }

    public function testGreaterThanOrEqual() {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;
        $expr = new Conditions;
        $expr->greaterThanOrEqual('view', 100);
        $sql = $expr->toSql($driver, $args);
        is("view >= 100", $sql);
    }

    public function testNotEqual() {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;
        $expr = new Conditions;
        $expr->notEqual('a', 1);
        $sql = $expr->toSql($driver, $args);
        is("a <> 1", $sql);
    }

    public function testIsNot() {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;

        $expr = new Conditions;
        $expr->isNot('is_book', TRUE);
        $sql = $expr->toSql($driver, $args);
        is("is_book IS NOT TRUE", $sql);


        $args = new ArgumentArray;
        $expr = new Conditions;
        $expr->isNot('is_book', FALSE);
        $sql = $expr->toSql($driver, $args);
        is("is_book IS NOT FALSE", $sql);

        $args = new ArgumentArray;
        $expr = new Conditions;
        $expr->isNot('is_book', new Unknown);
        $sql = $expr->toSql($driver, $args);
        is("is_book IS NOT UNKNOWN", $sql);
    }

    public function testOperatorMethod()
    {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;

        $conditions = new Conditions;
        $conditions->is('confirmed', TRUE)
            ->or()->is('approved', TRUE)
            ->and()->equal('points', 100)
            ;
        $sql = $conditions->toSql($driver, $args);
        is("confirmed IS TRUE OR approved IS TRUE AND points = 100", $sql);
    }


    public function testOperatorXor()
    {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;

        $conditions = new Conditions;
        $conditions->is('confirmed', TRUE)
            ->xor()->is('approved', TRUE)
            ;
        $sql = $conditions->toSql($driver, $args);
        is("confirmed IS TRUE XOR approved IS TRUE", $sql);
    }


    public function testConditionGroup()
    {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;

        $conditions = new Conditions;
        $conditions->is('confirmed', TRUE)
            ->or()->is('approved', TRUE)
            ->group()
                ->like('name', 'John')
                ->or()
                ->like('name', 'Mary')
            ->endgroup()
            ;
        $sql = $conditions->toSql($driver, $args);
        is("confirmed IS TRUE OR approved IS TRUE AND (name LIKE '%John%' OR name LIKE '%Mary%')", $sql);
    }

    public function testIs() {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;

        $expr = new Conditions;
        $expr->is('is_book', TRUE);
        $sql = $expr->toSql($driver, $args);
        is("is_book IS TRUE", $sql);

        $args = new ArgumentArray;
        $expr = new Conditions;
        $expr->is('is_book', FALSE);
        $sql = $expr->toSql($driver, $args);
        is("is_book IS FALSE", $sql);

        $args = new ArgumentArray;
        $expr = new Conditions;
        $expr->is('is_book', new Unknown);
        $sql = $expr->toSql($driver, $args);
        is("is_book IS UNKNOWN", $sql);
    }


    public function likeExprProvider() {
        return [
            [ NULL ,                 "John", "name LIKE '%John%'" ],
            [ Criteria::CONTAINS ,   "John", "name LIKE '%John%'" ],
            [ Criteria::STARTS_WITH, "John", "name LIKE 'John%'" ],
            [ Criteria::ENDS_WITH,   "John", "name LIKE '%John'" ],
        ];
    }



    /**
     * @dataProvider likeExprProvider
     */
    public function testLikeExpr($criteria, $pat, $expectedSql) {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;
        $expr = new Conditions;
        $expr->like('name', $pat, $criteria);
        $sql = $expr->toSql($driver, $args);
        is($expectedSql, $sql);
    }

    public function testRegExp()
    {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;
        $expr = new Conditions;
        $expr->regExp('content', '.*');
        $sql = $expr->toSql($driver, $args);
        is("content REGEXP '.*'", $sql);
    }

    public function testNotRegExp()
    {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;
        $expr = new Conditions;
        $expr->notRegExp('content', '.*');
        $sql = $expr->toSql($driver, $args);
        is("content NOT REGEXP '.*'", $sql);
    }

    public function testBetweenExpr()
    {
        $args = new ArgumentArray;
        $driver = new SQLBuilder\Driver\MySQLDriver;
        $expr = new Conditions;
        $expr->between('created_at', date('c') , date('c', time() + 3600));
        $sql = $expr->toSql($driver, $args);
        // is("", $sql);
    }
}

