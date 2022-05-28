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
