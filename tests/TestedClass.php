<?php

namespace Tests;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\SPrint;
use Bfg\Comcode\Traits\Conditionable;
use Traversable;

class TestedClass extends Comcode implements Traversable
{
    use Conditionable;
    protected const TEST2 = 2.2;
    /**
     * @var array|string[]
     */
    public array $table = ['aa'];
    private SPrint $users;
    private $test;

    public function test3(): void
    {
    }

    protected function test1()
    {
        return 'text1 - ';
    }

    protected function test4()
    {
        // exp
        $text = $this->test1()->zzz;
        // exp2
        $text += 2;
        // exp33
        $text -= 33;
        // exp23
        $text += 2311;
        // exp3
        $text += 1223;
        // exp311
        $text -= 311;
        echo 'here';
        echo 'must';
        echo 'be';
        echo '311';
        // exp11
        $text += 400;
        // exp5
        $text += 100;
        echo 'simpe tr';
        echo 'simpe tr';
        echo 'simpe tr';

        return max($text);
    }
}
