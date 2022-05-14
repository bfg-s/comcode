<?php

namespace Bfg\Comcode\Tests;

class CCClassBaseTest extends TestCase
{
    public function test_class_creating()
    {
        $class = $this->class();

        if (is_file($class->fileSubject->file)) {
            $this->assertTrue(true);
        } else {
            $class->fileSubject->delete();
            $this->fail();
        }
    }


    public function test_namespace()
    {
        $class = $this->class();

        $this->assertTrue(
            str_starts_with(
                $class, "<?php\n\nnamespace Bfg\\Comcode\\Tests"
            )
        );

        $class->fileSubject->delete();
    }
}
