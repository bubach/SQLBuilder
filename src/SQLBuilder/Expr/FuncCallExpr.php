<?php
namespace SQLBuilder\Expr;
use SQLBuilder\Expr\Expr;
use SQLBuilder\Driver\BaseDriver;
use SQLBuilder\ParamMarker;
use SQLBuilder\Criteria;
use SQLBuilder\ArgumentArray;
use SQLBuilder\ToSqlInterface;
use LogicException;

class FuncCallExpr implements ToSqlInterface
{

    public $funcName;

    public function __construct($funcName, array $args = array())
    {
        // code...
        $this->funcName = $funcName;
        $this->funcParams = new ListExpr($args);
    }

    public function toSql(BaseDriver $driver, ArgumentArray $args) {
        return $this->funcName . $this->funcParams->toSql($driver, $args);
    }
}



