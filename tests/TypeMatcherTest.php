<?php

namespace Bermuda\Reflection\Tests;

use Bermuda\Reflection\TypeMatcher;
use PHPUnit\Framework\TestCase;
use ReflectionFunction;

class TypeMatcherTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testBooleanParam()
    {
        $callback = static fn(bool $mode): bool => $mode;
        $reflector = new ReflectionFunction($callback);

        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), true, true);

        $this->assertTrue($result);
    }

    /**
     * @throws \ReflectionException
     */
    public function testArrayParam()
    {
        $callback = static fn(array $var): array => $var;
        $reflector = new ReflectionFunction($callback);

        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), [], true);

        $this->assertTrue($result);
    }

    /**
     * @throws \ReflectionException
     */
    public function testObjectParam()
    {
        $callback = static fn(object $var): object => $var;
        $reflector = new ReflectionFunction($callback);

        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), new \StdClass, true);

        $this->assertTrue($result);
    }

    public function testMixedParam()
    {
        $callback = static fn(mixed $var): mixed => $var;
        $reflector = new ReflectionFunction($callback);

        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), new \StdClass, true);

        $this->assertTrue($result);
    }

    public function testStringParam()
    {
        $callback = static fn(string $var): string => $var;
        $reflector = new ReflectionFunction($callback);

        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), '', true);
        $this->assertTrue($result);

        $var = new class implements \Stringable
        {
            public function __toString(): string
            {
                return '';
            }
        };

        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), $var, true);
        $this->assertFalse($result);
        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), $var);
        $this->assertTrue($result);
    }

    public function testIntParam()
    {
        $callback = static fn(int $var): int => $var;
        $reflector = new ReflectionFunction($callback);

        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), 1);
        $this->assertTrue($result);
        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), '1');
        $this->assertTrue($result);
        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), '1', true);
        $this->assertFalse($result);
    }

    public function testFloatParam()
    {
        $callback = static fn(float $var): float => $var;
        $reflector = new ReflectionFunction($callback);

        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), 1.1);
        $this->assertTrue($result);
        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), '1.1');
        $this->assertTrue($result);
        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), '1.1', true);
        $this->assertFalse($result);
    }

    public function testInstanceOfParam()
    {
        $callback = static fn(A $var): A => $var;
        $reflector = new ReflectionFunction($callback);

        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(),
            new class implements A {}
        );

        $this->assertTrue($result);
    }

    public function testIntersectionTypeParam()
    {
        $callback = static fn(A&B $var): A&B => $var;
        $reflector = new ReflectionFunction($callback);

        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(),
            new class implements A,B {}
        );

        $this->assertTrue($result);
    }

    public function testUnionTypeParam()
    {
        $callback = static fn(A|B $var): A|B => $var;
        $reflector = new ReflectionFunction($callback);

        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(),
            new class implements B {}
        );

        $this->assertTrue($result);
    }

    public function testAllowsNullParam()
    {
        $callback = static fn(null|A|B $var=null): A|B|null => $var;
        $reflector = new ReflectionFunction($callback);

        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), null);

        $this->assertTrue($result);
    }

    public function testCallableParam()
    {
        $callback = static fn(callable $var): callable => $var;
        $reflector = new ReflectionFunction($callback);

        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), 'strtoupper');

        $this->assertTrue($result);
    }

    public function testIterableParam()
    {
        $callback = static fn(iterable $var): iterable => $var;
        $reflector = new ReflectionFunction($callback);

        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), []);

        $this->assertTrue($result);
    }

    public function testInvalidParam()
    {
        $callback = static fn(int $var): int => $var;
        $reflector = new ReflectionFunction($callback);

        $result = TypeMatcher::match($reflector->getParameters()[0]->getType(), '50', true);
        $this->assertFalse($result);
    }

}

interface A
{
}

interface B
{
}
