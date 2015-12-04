# axy\phpcode-compile

[![Latest Stable Version](https://img.shields.io/packagist/v/axy/phpcode-compile.svg?style=flat-square)](https://packagist.org/packages/axy/phpcode-compile)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.4-8892BF.svg?style=flat-square)](https://php.net/)
[![Build Status](https://img.shields.io/travis/axypro/phpcode-compile/master.svg?style=flat-square)](https://travis-ci.org/axypro/phpcode-compile)

Generation PHP code

* GitHub: [axypro/phpcode-compile](https://github.com/axypro/phpcode-compile)
* Composer: [axy/phpcode-compile](https://packagist.org/packages/axy/phpcode-compile)

PHP 5.4+

The library does not require any dependencies (except composer packages).

### Lambda

Creates an anonymous function by a code.

```php
use axy\phpcode\compile;

$code = 'return $x * $y';

$lambda = new Lambda($code, ['x', 'y']);
echo $lambda(2, 2); // 4
```

##### `__construct(mixed $code [, array $args [, array $uses])`

* `$code` - a string of the PHP code (or an array of strings)
* `$args` - a list of arguments (by default no arguments)
* `$uses` - a list of uses classes (see the below example)

```php
$uses = [
    'common\Helper as CommonHelper',
];

$lambda = new Lambda('return CommonHelper::method($x)', ['x'], $uses);

$lambda(5); // common\Helper::method(5)
```

The resulting code:

```php
use common\Helpers as CommonHelper;

return function ($x) {
    return CommonHelper::method($x);
};
```

##### `appendCodeLine(string $line)`

Appends a line to the end of code.

##### `getInnerCode(void): string`

Returns an inner code of the lambda.

```php
$lambda = new Lambda('$x = 1;');
$lambda->appendCodeLine('return $x + 1;');
echo $lambda->getInnerCode();
```

Result:

```php
$x = 1;
return $x + 1;
```

##### `getArgs(void): string[]`
##### `getUsed(void): string`

Getters of the args and used lists.

##### `getOuterCode(void): string`

The code of the lambda generation:

```php
return function($x) {
    $x = 1;
    return $x + 1;
}
```

##### `getCallback(void): callable`
##### `call(array $args): mixed`
##### `__invoke(...): mixed`

```php
$callback = $lambda->getCallback();
$callback(1, 2, 3);
```

Analogue

```php
$lambda->call([1, 2, 3]);
```

Analogue

```php
$lambda(1, 2, 3);
```

##### `getContentForFile([string $comment])`
##### `save(string $filename [, string $comment])`

Get content for a php file or save this file.
This file returns the lambda.

```php
$lambda->save('lambda.php', 'Auto generated');

// ...

$lambda2 = (include 'lambda.php');
$lambda2(1, 2, 3);
```
