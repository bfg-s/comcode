# ComCode

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/bfg/comcode.svg?style=flat-square)](https://packagist.org/packages/bfg/comcode)

## Install

```bash
composer require bfg/comcode
```

## Description

A special engine based on the [nikic/PHP-Parser](https://github.com/nikic/PHP-Parser)
and [FriendsOfPHP/PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer).
This core describes the middle layer, this is neat control of the controlled nodes and the inviolability of its own.

## Example

```php
use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\AlwaysLastNodeInterface;
...
$class = php()->class(\Tests\TestedClass::class);
$class->extends(Comcode::class);
$class->implement(AlwaysLastNodeInterface::class);
$class->trait(Conditionable::class);
$class->protectedConst('const1', 1);
$class->protectedConst('const2', 1.1);
$class->protectedConst('const3', false);
$class->protectedConst('const3', ['success', 'error', 'warning']);

$class->publicProperty('array:tables', ['users', 'migrations']);
$class->protectedProperty('settings', false);
$class->privateProperty([Comcode::class, 'engine']);

$class->comment(function (DocSubject $docSubject) {
    $docSubject->description('Description of test class');
    $docSubject->tagMethod('static', 'get()');
});

$method1 = $class->protectedMethod('method1');

$method1->return()->real('text');

$method2 = $class->publicMethod('method2');

$method2->comment(function (DocSubject $doc) {
    $doc->name('Method 2');
    $doc->name('And him Description');
    $doc->tagReturn('string');
});

$method2->expectParams(['wait', Comcode::defineValueNode(null), Node::class]);

$method2->row('test row 1')
    ->var('wait')->assign('this')->methodWithProps(1, function (ClosureNode $q) {
        $q->expectParams(['param1', 'default text', 'string']);
        $q->row('inner test row 1')->var('param1')->concat(php()->real(' and true text'));
        $q->row('inner test row 2')->var('param1')->concat(php()->real(' and false text'));
        $q->return('param1');
    })->method()->property->someMethod()->methodWithProps(function (ClosureNode $q) {
        $q->row('inner test row 1.1')->var('param1')->assign(php()->real(100));
        $q->row('inner test row 1.2')->var('param1')->plus(php()->real(200));

        $q->return('param1')->func('func', function (ClosureNode $node) {
            $node->row('inner test row 1.2.1')->var('name')->concat(php()->real(' Xsaven'));
            $node->expectParams('name')->return('name');
        });
    }, fn(ClosureNode $node) => $node->expectParams('name')->return()->var('name'));

$method2->return('wait');

$method3 = $class->protectedMethod('method3');
$method3->comment('// Simple comment for method 3');

$method3->row('test row 1')
    ->var('text')
    ->assign('this')->property;

$method3->row('test row 2')
    ->var('text')
    ->plus(
        php()->real(2)
    );

$method3->row('test row 3')
    ->var('text')
    ->minus(
        php()->real(33)
    );

$method3->row('test row 4')
    ->var('text')
    ->concat(
        php()->real(2311)
    );

$method3->row('test row 5')
    ->var('text')
    ->plus(
        php()->real(1223)
    );

$method3->row('test row 6')
    ->var('text')
    ->minus(
        php()->real(311)
    );

$method3->row('test row 7')
    ->var('text')
    ->plus(
        php()->real(400)
    );

$method3->row('test row 8')
    ->var('text')
    ->plus(
        php()->real(100)
    );

$method3->return()->func('max', php('text'));

$class->save();
```

## Result
```php
<?php

namespace Tests;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\AlwaysLastNodeInterface;
use Bfg\Comcode\Node;
use Bfg\Comcode\Traits\Conditionable;

/**
 * Description of test class.
 *
 * @method static get()
 */
class TestedClass extends Comcode implements AlwaysLastNodeInterface
{
    use Conditionable;
    protected const CONST1 = 1;
    protected const CONST2 = 1.1;
    protected const CONST3 = ['success', 'error', 'warning'];
    public array $tables = ['users', 'migrations'];
    protected $settings = false;
    private Comcode $engine;

    /**
     * And him Description.
     *
     * @return string
     */
    public function method2(Node $wait = null)
    {
        // test row 1
        $wait = $this->methodWithProps(1, function (string $param1 = 'default text') {
            // inner test row 1
            $param1 .= ' and true text';
            // inner test row 2
            $param1 .= ' and false text';

            return $param1;
        })->method()->property->someMethod()->methodWithProps(function () {
            // inner test row 1.1
            $param1 = 100;
            // inner test row 1.2
            $param1 += 200;

            return $param1->func(function ($name) {
                // inner test row 1.2.1
                $name .= ' Xsaven';

                return $name;
            });
        }, fn ($name) => $name);

        return $wait;
    }

    protected function method1()
    {
        return 'text';
    }

    // Simple comment for method 3
    protected function method3()
    {
        // test row 1
        $text = $this->property;
        // test row 2
        $text += 2;
        // test row 3
        $text -= 33;
        // test row 4
        $text .= 2311;
        // test row 5
        $text += 1223;
        // test row 6
        $text -= 311;
        // test row 7
        $text += 400;
        // test row 8
        $text += 100;

        return max($text);
    }
}

```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security-related issues, please email xsaven@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.
