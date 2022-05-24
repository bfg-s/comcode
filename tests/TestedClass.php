<?php

namespace Tests;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\SPrint;
use Traversable;

function test()
{
}
test();

class TestedClass extends Comcode implements Traversable
{
    protected const TEST2 = 2.2;
    public array $table = ['aa'];
    private SPrint $users;
    private $test;

    protected function test1()
    {
        return 1;
    }

    protected function test2()
    {
        return $this->users->hasOne()->where();
    }

    protected function test3()
    {
        echo 123;
        if (1) {
        } else {
            if (2) {
            }
        }
    }
}
