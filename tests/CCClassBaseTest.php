<?php

namespace Bfg\Comcode\Tests;

class CCClassBaseTest extends TestCase
{
    public function test_class_creating()
    {
        $this->class()->save();

        $this->assertTrue(
            is_file($this->class()->fileSubject->file)
        );

        $this->class()->delete();
    }

    public function test_namespace()
    {
        $this->class()->save();

        $this->assertClassContains(
            '<?php*namespace Tests*'
        );

        $this->class()->delete();
    }
}
