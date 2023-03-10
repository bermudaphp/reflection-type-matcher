<?php

namespace Bermuda\Reflection;

final class TypeMatcher
{
    public function match(\ReflectionType $type, mixed $var): bool
    {
        if ($type instanceof \ReflectionNamedType) return $this->matchNamedType($type, $var);
        if ($type instanceof \ReflectionUnionType) return $this->matchUnionType($type, $var);
        if ($type instanceof \ReflectionIntersectionType) return $this->matchIntersectionType($type, $var);

        throw new \InvalidArgumentException(sprintf('%s type not supported', $type::class));
    }

    private function matchNamedType(\ReflectionNamedType $type, mixed $var): bool
    {
        if ($type->allowsNull() && $var === null) return true;
        if ($type->isBuiltin()) return ('is_'.$type->getName())($var);

        return $this->isInstanceOf($var, $type->getName());
    }

    private function matchUnionType(\ReflectionUnionType $type, mixed $var): bool
    {
        if ($type->allowsNull() && $var === null) return true;
        foreach ($type->getTypes() as $type) {
            if ($this->match($type, $var)) return true;
        }

        return false;
    }

    private function matchIntersectionType(\ReflectionIntersectionType $type, mixed $var): bool
    {
        if ($type->allowsNull() && $var === null) return true;
        foreach ($type->getTypes() as $type) {
            if (!$this->match($type, $var)) return false;
        }

        return true;
    }
    
    private function isInstanceOf(mixed $obj, string $cls): bool
    {
        return $obj instanceof $cls;
    }
}
