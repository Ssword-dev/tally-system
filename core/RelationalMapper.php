<?php
// ORM functionality here.

namespace Core;

trait HasClauses
{
    private array $clauses = []; // {cls: Clauses[]}

    public function addClause(BaseClause $clause)
    {
        $this->clauses[$clause::class] ??= [];
        $this->clauses[$clause::class][] = $clause;
        return $clause;
    }

    public function getClauses(): array
    {
        return array_merge(...array_values($this->clauses));
    }


    public function getClausesOfType($clauseType)
    {
        return $this->clauses[$clauseType] ?? [];
    }
}

abstract class BaseClause
{
}

/**
 * @template T of BaseQueryable
 */
abstract class BaseStatement
{
    public function __construct()
    {
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

final class SelectStatement extends BaseStatement
{
    public $fromTable;

    public function from(string $table)
    {
        $this->fromTable = $table;
        return $this;
    }

    public function join()
    {
    }
}

// Select Statement Clauses.
// Join Clause.
final class JoinClause extends BaseClause
{
    use HasClauses;

    public function on()
    {
        return $this->addClause(new JoinOnSubclause());
    }
}

// Join Subclauses.
final class JoinOnSubclause extends BaseClause
{

}