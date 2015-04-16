<?php
namespace SQLBuilder\MySQL\Syntax;
use SQLBuilder\ToSqlInterface;
use SQLBuilder\Driver\BaseDriver;
use SQLBuilder\Driver\MySQLDriver;
use SQLBuilder\Driver\PgSQLDriver;
use SQLBuilder\ArgumentArray;
use SQLBuilder\Universal\Traits\KeyTrait;
use SQLBuilder\Universal\Syntax\Column;
use SQLBuilder\Exception\UnsupportedDriverException;

class AlterTableSetAutoIncrement implements ToSqlInterface
{
    protected $value;

    public function __construct($value) 
    {
        $this->value = $value;
    }

    public function toSql(BaseDriver $driver, ArgumentArray $args) 
    {
        return 'AUTO_INCREMENT = ' . $driver->deflate($this->value);
    }
}




