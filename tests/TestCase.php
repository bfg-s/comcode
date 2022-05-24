<?php

namespace Bfg\Comcode\Tests;

use Bfg\Comcode\Subjects\ClassSubject;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function class(): ClassSubject
    {
        return php()->class(\Tests\TestedClass::class);
    }
}
