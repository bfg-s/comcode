<?php

namespace Bfg\Comcode\Tests;

use Bfg\Comcode\Traits\Conditionable;
use Bfg\Comcode\Traits\FuncCommonTrait;

class CCClassTraitTest extends TestCase
{
    public function test_class_traits()
    {
        $class = $this->resetClass()->class();
        $class->trait(FuncCommonTrait::class);
        $class->trait(Conditionable::class);
        $class->save();
        $this->assertClassContains('use Bfg\Comcode\Traits\FuncCommonTrait;');
        $this->assertClassContains('use Bfg\Comcode\Traits\Conditionable;');
        $this->assertClassContains('use FuncCommonTrait;');
        $this->assertClassContains('use Conditionable;');
        $class->forgetTrait(FuncCommonTrait::class);
        $class->forgetTrait(Conditionable::class);
        $class->save();
        $this->assertClassNotContains('use Bfg\Comcode\Traits\FuncCommonTrait;');
        $this->assertClassNotContains('use Bfg\Comcode\Traits\Conditionable;');
        $this->assertClassNotContains('use FuncCommonTrait;');
        $this->assertClassNotContains('use Conditionable;');
        $this->class()->delete();
    }
}
