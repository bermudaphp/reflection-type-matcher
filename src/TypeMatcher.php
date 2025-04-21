<?php

namespace Bermuda\Reflection;

/**
 * The TypeMatcher class is designed to verify whether a given variable matches
 * a specific reflected type.
 */
final class TypeMatcher
{
    /**
     * Main method to check if a variable matches a given type.
     *
     * @param \ReflectionType $type The reflected type that the variable should match.
     * @param mixed $var The variable to validate against the type.
     * @param bool $strict If true, strict comparisons are used for built-in types.
     *
     * @return bool Returns true if the variable matches the type; otherwise, false.
     *
     * @throws \InvalidArgumentException If the type is not supported.
     */
    public static function match(\ReflectionType $type, mixed $var, bool $strict = false): bool
    {
        if ($type instanceof \ReflectionNamedType) return self::matchNamedType($type, $var, $strict);
        if ($type instanceof \ReflectionUnionType) return self::matchUnionType($type, $var, $strict);
        if ($type instanceof \ReflectionIntersectionType) return self::matchIntersectionType($type, $var, $strict);

        throw new \InvalidArgumentException(sprintf('%s type not supported', $type::class));
    }

    /**
     * Checks if a variable matches a named type.
     *
     * @param \ReflectionNamedType $type The reflected named type.
     * @param mixed $var The variable to check.
     * @param bool $strict If true, applies strict comparison for built-in types.
     *
     * @return bool Returns true if the variable matches the type; otherwise, false.
     */
    private static function matchNamedType(\ReflectionNamedType $type, mixed $var, bool $strict): bool
    {
        if ($type->allowsNull() && $var === null) return true;
        if ($type->isBuiltin()) return self::matchBuiltinType($type, $var, $strict);
        
        return self::isInstanceOf($var, $type->getName());
    }

    /**
     * Checks if a variable matches a built-in (primitive) type.
     *
     * @param \ReflectionType $type The reflected built-in type.
     * @param mixed $var The variable to check.
     * @param bool $strict If true, uses strict type checking (e.g., is_int for int).
     *
     * @return bool Returns true if the variable matches the built-in type; otherwise, false.
     */
    private static function matchBuiltinType(\ReflectionType $type, mixed $var, bool $strict): bool
    {
        return match ($type->getName()) {
            'mixed' => true, 
            'object' => is_object($var), 
            'array' => is_array($var), 
            'bool' => is_bool($var), 
            'int' => $strict ? is_int($var) : is_numeric($var),
            'float' => $strict ? is_float($var) : is_numeric($var), 
            'string' => $strict ? is_string($var) : (is_string($var) || $var instanceof \Stringable), 
            'false' => $var === false,
            'true' => $var === true, 
            'callable' => is_callable($var),
            'iterable' => is_iterable($var),
            default => false 
        };
    }

    /**
     * Checks if a variable matches a union type (a type representing multiple possible types).
     *
     * @param \ReflectionUnionType $type The reflected union type.
     * @param mixed $var The variable to check.
     * @param bool $strict If true, applies strict comparisons.
     *
     * @return bool Returns true if the variable matches at least one type in the union; otherwise, false.
     */
    private static function matchUnionType(\ReflectionUnionType $type, mixed $var, bool $strict): bool
    {
        if ($type->allowsNull() && $var === null) return true;
        foreach ($type->getTypes() as $type) {
            if (self::match($type, $var, $strict)) return true;
        }

        return false;
    }

    /**
     * Checks if a variable matches an intersection type (a type that requires matching all specified types).
     *
     * @param \ReflectionIntersectionType $type The reflected intersection type.
     * @param mixed $var The variable to check.
     * @param bool $strict If true, applies strict comparisons.
     *
     * @return bool Returns true if the variable matches every type in the intersection; otherwise, false.
     */
    private static function matchIntersectionType(\ReflectionIntersectionType $type, mixed $var, bool $strict): bool
    {
        if ($type->allowsNull() && $var === null) return true;
        foreach ($type->getTypes() as $type) {
            if (!self::match($type, $var, $strict)) return false;
        }

        return true;
    }

    /**
     * Determines whether the given variable is an instance of the specified class.
     *
     * @param mixed $obj The variable to check.
     * @param string $cls The class name to check against.
     *
     * @return bool Returns true if the variable is an instance of the specified class; otherwise, false.
     */
    private static function isInstanceOf(mixed $obj, string $cls): bool
    {
        return $obj instanceof $cls;
    }
}
