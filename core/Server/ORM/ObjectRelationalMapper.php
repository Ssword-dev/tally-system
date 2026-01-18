<?php
use Core\Server\ORM\Attributes\Column;
use Core\Server\ORM\Attributes\Computed;
use Core\Server\Traits\Singleton;

// helper classes
final class ReflectionStore
{
    use Singleton;

    public array $reflectionCache = [];

    public function computed($op, $targetIdentifiers, $computeFn)
    {
        $hashSourceSegments = implode(':', $targetIdentifiers);
        $hash = md5($hashSourceSegments);
        return $this->reflectionCache[$hash] ?? ($this->reflectionCache[$hash] = $computeFn());
    }

    public function getClassReflection($cls): ReflectionClass
    {
        return $this->computed('getClassReflection', [$cls], fn() => new ReflectionClass($cls));
    }

    public function getAttributesOfType($cls, $type)
    {
        return $this->computed('getAttributesOfType', [$cls, $type], fn() => $this->getClassReflection($cls)->getAttributes($type));
    }
}

final class ObjectRelationalMapper
{
    public const ColumnAttribute = Column::class;
    public const ComputedAttribute = Computed::class;

    public function __construct()
    {
    }


    private function getConfig(string $modelClass)
    {
    }

    private function map(array $data, string $modelClass)
    {

    }
}