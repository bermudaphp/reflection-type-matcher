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
