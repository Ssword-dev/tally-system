<?php

namespace Core\Server\Models;

use Core\Server\Attributes\ModelFieldAttribute;
use Exception;
use \ReflectionClass;
use \ReflectionProperty;

/**
 * The base of all model class.
 * 
 * This class provides functionality to map database rows
 * to model instances, as well as caching reflection results
 * for performance optimization.
 * 
 * This lets you define your model as normal classes but
 * have the ability to map database rows to model instances
 * automatically.
 * 
 * Note that this is not an ORM, as it does not provide
 * the full functionality of an ORM, such as querying,
 * relationships, etc. It only provides the mCore\Servering
 * functionality.
 */
#[\AllowDynamicProperties]
abstract class BaseModel
{
    // cached reflection results
    // the base model is the only one
    // that should manage this.
    private static ?array $propertyNames = null;
    private static ?array $propertyConfigurations = null;

    // instantiate a model from DB row
    public static function map(array $data): static
    {

        $properties = static::getInstanceProperties();
        $instance = new static();

        foreach ($properties['configurations'] as $modelField => $config) {
            if (isset($config['columnName'])) {
                $dbField = $config['columnName'];
                $instance->$modelField = $data[$dbField] ?? null;
            }
        }

        return $instance;
    }

    // get cached property names and attribute configurations
    public static function getInstanceProperties(): array
    {
        if (static::$propertyNames !== null && static::$propertyConfigurations !== null) {
            return [
                'names' => static::$propertyNames,
                'configurations' => static::$propertyConfigurations
            ];
        }

        $reflection = new ReflectionClass(static::class);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        $propertyNames = [];
        $propertyConfigurations = [];
        $hashTable = [];

        foreach ($properties as $property) {
            $name = $property->getName();
            $propertyNames[] = $name;
            $hashTable[$name] = true;

            $attrs = $property->getAttributes(ModelFieldAttribute::class);
            if (count($attrs) > 0) {
                /**
                 * @var ModelFieldAttribute
                 */
                $attributeInstance = $attrs[0]->newInstance();

                // Delegate the conversion to array to the attribute class,
                // this lets me change it anytime, anywhere and not have to
                // update this method.
                $propertyConfigurations[$name] = $attributeInstance->toArray();
            } else {
                $propertyConfigurations[$name] = [];
            }
        }

        // cache results for future calls
        static::$propertyNames = $propertyNames;
        static::$propertyConfigurations = $propertyConfigurations;

        return [
            'names' => $propertyNames,
            'configurations' => $propertyConfigurations
        ];
    }

    // check if a property exists efficiently
    public static function hasProperty(string $propertyName): bool
    {
        if (static::$propertyConfigurations === null) {
            static::getInstanceProperties();
        }

        return isset(static::$propertyConfigurations[$propertyName]);
    }

    public function __construct()
    {
        // late initialization.
    }
}