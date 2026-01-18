<?php
namespace Core\Server\ORM\Attributes;

use \Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Column
{
    public string $columnName;

    public function __construct(string $columnName)
    {
        $this->columnName = $columnName;
    }

    public function toArray(): array
    {
        return [
            'columnName' => $this->columnName,
        ];
    }
}