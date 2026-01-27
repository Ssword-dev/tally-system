<?php
// ORM functionality here.

namespace Core;

abstract class BaseClause
{

}

/**
 * @template T of BaseQueryable
 */
abstract class BaseStatement
{
    /**
     * @var T
     */
    public BaseQueryable $queryable;

    /**
     * @param T $dialect
     */
    public function __construct($queryable)
    {
        $this->queryable = $queryable;
    }

    /**
     * @return T
     */
    public function endStatement()
    {
        $this->queryable->statements[] = $this;
        return $this->queryable;
    }
}

abstract class BaseQueryable
{
    /**
     * @var BaseStatement[]
     */
    public array $statements;

    public function __construct()
    {
        $this->statements = [];
    }
}
