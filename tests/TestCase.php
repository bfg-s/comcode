<?php

namespace Bfg\Comcode\Tests;

use Bfg\Comcode\Subjects\ClassSubject;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function class(): ClassSubject
    {
        return php()->class(TestedClass::class);
    }
}
