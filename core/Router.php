<?php
// Definition for router classes here.
namespace Core;

abstract class BaseClause
{

}

/**
 * @template T of BaseDialect
 */
abstract class BaseStatement
{
    /**
     * @var T
     */
    public BaseDialect $dialect;

    /**
     * @param T $dialect
     */
    public function __construct($dialect)
    {
        $this->dialect = $dialect;
    }

    /**
     * @return T
     */
    public function endStatement()
    {
        $this->dialect->statements[] = $this;
        return $this->dialect;
    }
}

abstract class BaseDialect
{
    /**
     * @var BaseStatement[]
     */
    public array $statements;

    public function __construct()
    {
        $this->statements = [];
    }

    abstract public function toSql();
}
