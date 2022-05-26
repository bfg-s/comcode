<?php

namespace Bfg\Comcode\Tests;

class CCClassBaseTest extends TestCase
{
    public function test_class_creating()
    {
        if (is_file($file = __DIR__ . '/TestedClass.php')) {
            unlink($file);
        }

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
                $class, "<?php\n\nnamespace Tests"
            )
        );

        $class->fileSubject->delete();
    }

    public function test_has_uses()
    {
        $this->assertClassContains(
            'use Bfg\Comcode\Comcode;'
        );
        $this->assertClassContains(
            'use Traversable;'
        );
    }

    public function test_has_extends()
    {
        $this->assertClassContains(
            ' extends Comcode'
        );
    }

    public function test_has_implements()
    {
        $this->assertClassContains(
            ' implements Traversable'
        );
    }
}
