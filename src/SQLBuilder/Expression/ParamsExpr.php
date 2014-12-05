<?php
namespace SQLBuilder\Expression;

use SQLBuilder\Expression\Expr;
use SQLBuilder\Driver\BaseDriver;
use SQLBuilder\ParamMarker;
use LogicException;

/**
 * This class is used for handling function parameters
 */
class ParamsExpr extends Expr { 

    public $params;

    public function __construct(array $params) {
        $this->params = $params;
    }

    public function append($val) {
        $this->params[] = $val;
    }

    public function renderSet(BaseDriver $driver, array $set) 
    {
        return array_map(function($val) use($driver) {
            return $driver->deflate($val);
        }, $set);
    }

    public function toSql(BaseDriver $driver) 
    {
        return join(',', $this->renderSet($driver, $this->params));
    }

}

