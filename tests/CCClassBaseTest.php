<?php

namespace Bfg\Comcode\Tests;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\AlwaysLastNodeInterface;

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

    public function test_exists_method()
    {
        $this->resetClass()->class()->save();

        $this->assertTrue(
            $this->class()->existsExtends(
                Comcode::class
            )
        );

        $this->assertTrue(
            $this->class()->existsImplement(
                AlwaysLastNodeInterface::class
            )
        );

        $this->class()->delete();
    }

    public function test_easy_coding_upgrade()
    {
        $this->resetClass()->class();

        $method = $this->class()->publicMethod('rules');

        $method->return()->real([
            'name' => ['test','test','test','test','test'],
            'name1' => ['test','test','test','test','test'],
            'name2' => ['test','test','test','test','test'],
        ]);

        $this->class()->save()->standard();

        $this->assertClassContains('        return [');
        $this->assertClassContains("            'name' => ['test', 'test', 'test', 'test', 'test'],");
        $this->assertClassContains("            'name1' => ['test', 'test', 'test', 'test', 'test'],");
        $this->assertClassContains("            'name2' => ['test', 'test', 'test', 'test', 'test']");
        $this->assertClassContains('        ];');

//        $this->class()->delete();
    }
}
