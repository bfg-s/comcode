<?php

namespace Bfg\Comcode\Tests;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\AlwaysLastNodeInterface;
use Bfg\Comcode\Subjects\ClassSubject;
use ErrorException;
use JetBrains\PhpStorm\NoReturn;
use Traversable;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public ?ClassSubject $class = null;

    protected function assertClassContains(
        string|array $needle,
    ) {
        $assert = false;
        $subject = $this->class()->content();
        foreach ((array) $needle as $item) {
            $item = str_contains($item, '*') ? $item : "*$item*";
            if (! Comcode::is($item, $subject)) {
                $this->fail($subject.' != '.$item);
            } else {
                $assert = true;
            }
        }
        $this->assertTrue($assert);
    }

    protected function assertClassNotContains(
        string|array $needle,
    ) {
        $assert = false;
        $subject = $this->class()->content();
        foreach ((array) $needle as $item) {
            $item = str_contains($item, '*') ? $item : "*$item*";
            if (Comcode::is($item, $subject)) {
                $this->fail($subject.' == '.$item);
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
        if (is_file($file = __DIR__ . '/TestedClass.php')) {
            unlink($file);
        }

        $this->class = null;

        return $this;
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
            $needle . ' = ' . $node
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
}
