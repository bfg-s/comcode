<?php

namespace Bfg\Comcode\Tests;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\AlwaysLastNodeInterface;
use Bfg\Comcode\Subjects\AnonymousClassSubject;
use Bfg\Comcode\Subjects\ClassSubject;
use Bfg\Comcode\Subjects\EnumSubject;
use Bfg\Comcode\Subjects\InterfaceSubject;
use Bfg\Comcode\Subjects\TraitSubject;
use ErrorException;
use JetBrains\PhpStorm\NoReturn;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public ?ClassSubject $class = null;

    /**
     * @param  $node
     * @param  string  $needle
     * @return void
     */
    public function assertNodePrintLike(
        $node,
        string $needle,
    ): void {
        $node = $this->print($node);
        $this->assertTrue(
            $node === $needle,
            $needle.' = '.$node
        );
    }

    /**
     * @param $node
     * @return string
     */
    public function print($node): string
    {
        return Comcode::printStmt($node);
    }

    #[NoReturn] public function ddPrint($node)
    {
        dd($this->print($node));
    }

    protected function assertClassContains(
        string|array $needle,
    ) {
        $assert = false;
        $subject = $this->class()->content();
        foreach ((array) $needle as $item) {
            $item = str_contains($item, '*') ? $item : "*$item*";
            if (!Comcode::is($item, $subject)) {
                $this->fail($subject."\nNot exists: ".$item);
            } else {
                $assert = true;
            }
        }
        $this->assertTrue($assert);
    }

    protected function class(): ClassSubject
    {
        if (!$this->class) {
            $this->class = php()->class(\Tests\TestedClass::class);
            $this->class->extends(Comcode::class);
            $this->class->implement(AlwaysLastNodeInterface::class);
        }
        return $this->class;
    }

    /**
     * @throws ErrorException
     */
    protected function trait(): TraitSubject
    {
        return php()->trait(\Tests\TestedTrait::class);
    }

    /**
     * @throws ErrorException
     */
    protected function enum(): EnumSubject
    {
        return php()->enum(\Tests\MyEnum::class);
    }

    /**
     * @throws ErrorException
     */
    protected function interface(): InterfaceSubject
    {
        $class = php()->interface(\Tests\TestedInterface::class);
        $class->extends(AlwaysLastNodeInterface::class);
        return $class;
    }

    protected function anonymousClass(): AnonymousClassSubject
    {
        $class = php()->anonymousClass('tests/anonymous.php', 'Tests');
        $class->extends(Comcode::class);
        $class->implement(AlwaysLastNodeInterface::class);

        return $class;
    }

    /**
     * @throws ErrorException
     */
    protected function anonymousClassNoNamespace(): AnonymousClassSubject
    {
        $class = php()->anonymousClass( 'tests/anonymous.php');
        $class->extends(Comcode::class);
        $class->implement(AlwaysLastNodeInterface::class);

        return $class;
    }

    protected function assertClassNotContains(
        string|array $needle,
    ) {
        $assert = false;
        $subject = $this->class()->content();
        foreach ((array) $needle as $item) {
            $item = str_contains($item, '*') ? $item : "*$item*";
            if (Comcode::is($item, $subject)) {
                $this->fail($subject."\nExists: ".$item);
            } else {
                $assert = true;
            }
        }
        $this->assertTrue($assert);
    }

    /**
     * @return $this
     */
    protected function resetClass(): static
    {
        if (is_file($file = __DIR__.'/TestedClass.php')) {
            unlink($file);
        }

        $this->class = null;

        return $this;
    }
}
