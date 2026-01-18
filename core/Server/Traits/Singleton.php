<?php
namespace Core\Server\Traits;

use Core\Server\Exceptions\SingletonDeserializationException;
use Core\Server\Exceptions\SingletonSerializationException;
use InitialArguments;
use TransformedArguments;

/**
 * # Singleton Trait
 * A common pattern used in programming.
 * Once a class uses this trait, it cannot be instantiated,
 * cloned, or deserialized directly.
 * 
 * Note that this is not the traditional singleton pattern,
 * as this is an improved version that separates the singleton
 * class into 3 phases, namely:
 * 
 * - Argument Preparation (getInstanceParams)
 * - Construction   (__construct)
 * - Initialization (initInstance)
 * 
 * You can override two of these phases, namely:
 * 
 * - Argument Preparation (getInstanceParams)
 * - Initialization (initInstance)
 * 
 * Overriding these methods allows you to customize the
 * behavior of the singleton instance creation without
 * doing it all in the constructor, Hence, the constructor
 * shall not be implemented in the class using this trait since
 * it is already implemented here as a private method.
 * 
 * This allows for better separation of concerns, as well as
 * more flexibility in the singleton class.
 * 
 * The methods below are explained in detail.
 * 
 * ## Methods
 * ### `__construct`
 * A private constructor to prevent direct instantiation,
 * ensuring the singleton pattern is maintained.
 * The constructor is marked as final to prevent overriding in
 * favor of the `initInstance` method.
 * 
 * ### `getInstance`
 * The `getInstance` method gets the singleton instance of the
 * current class.
 * 
 * ### `getInstanceParams`
 * The `getInstanceParams` method allows the current class to
 * override the parameters provided in the `getInstance` method,
 * after which the `initInstance` method is called with the new
 * parameters.
 * 
 * ### `initInstance`
 * The `initInstance` method allows the current class to
 * initialize the singleton instance after construction of
 * the singleton instance.
 * 
 * ## Sentinel Methods
 * ### `__clone`
 * The `__clone` method is made private to prevent cloning of the
 * singleton instance, throwing an exception if attempted.
 * 
 * ### `__serialize`
 * The `__serialize` method is made to prevent serialization of the
 * singleton instance, throwing an exception if attempted.
 * 
 * ### `__wakeup` && `__unserialize`
 * These sentinel methods are made to prevent deserialization of the
 * singleton instance, throwing an exception if attempted.
 * 
 * @template InitialArguments of array
 * @template TransformedArguments of array
 */
trait Singleton
{
    protected static array $instances = [];

    private function __construct()
    {
    }

    // making this private alone prevents cloning.
    private function __clone(): void
    {
    }

    // making these final prevents overriding in the class
    // using this trait.
    final public function __wakeup(): void
    {
        throw new SingletonDeserializationException("Cannot deserialize a singleton.");
    }

    final public function __serialize(): array
    {
        throw new SingletonSerializationException("Cannot serialize a singleton.");
    }

    final public function __unserialize(array $data): void
    {
        throw new SingletonDeserializationException("Cannot deserialize a singleton.");
    }

    /**
     * Gets the singleton instance of the current class.
     * @param mixed $args
     * @return static
     */
    final public static function getInstance(array ...$args): static
    {
        $class = static::class;

        if (!isset(static::$instances[$class])) {
            $instanceArgs = static::getInstanceParams(...$args);
            $instance = new static();

            static::$instances[$class] = $instance;

            static::initInstance(
                $instance,
                ...$instanceArgs
            );
        }

        return static::$instances[$class];
    }

    /**
     * Allows you to override parameters passed into the constructor.
     * @param InitialArguments ...$args
     * @return TransformedArguments
     */
    protected static function getInstanceParams(...$args): array
    {
        return $args;
    }

    /**
     * Allows the initialization of the singleton instance.
     * @param static $instance
     * @param InitialArguments $args
     * @return void
     */
    protected static function initInstance($instance, ...$args): void
    {
    }
}
