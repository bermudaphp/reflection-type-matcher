# TypeMatcher

**TypeMatcher** is a robust PHP class for dynamic type validation using the Reflection API. It helps you determine whether a given variable conforms to a specified reflected type, supporting named types, union types, and intersection types. The class is designed for PHP 8 and later, taking advantage of modern language features like match expressions and strict type comparisons.

## Features

- **Named Type Matching**: Checks if a variable is an instance of a specified class or a built-in type.
- **Union Type Matching**: Validates the variable against multiple possible types, ensuring it matches at least one.
- **Intersection Type Matching**: Ensures the variable conforms to every type in a given intersection.
- **Strict Comparison Option**: Use strict checks for numeric and string types or allow flexible comparisons when needed.
- **Lightweight & Standalone**: Easily integrate this class into your projects without the overhead of a larger framework.

# Install
```bash
composer require bermudaphp/reflection-type-matcher
```

# Usage
```php
    $reflector = new ReflectionFunction(static fn(int $a, int $b) => $a + $b);
    $param = $reflector->getParameters()[0];
    
    $matcher = new TypeMatcher();
    
    $matcher->match($param->getType(), '22'); // true
    $matcher->match($param->getType(), 22); // true
    $matcher->match($param->getType(), '22', true); // false
    
    $reflector = new ReflectionFunction(static fn(A&B $arg) => $arg);
    $param = $reflector->getParameters()[0];
    
    $matcher->match($param->getType(), new class implements A, B {}) // true
    $matcher->match($param->getType(), new StdClass) // false
   
```
