<?php

namespace Bfg\Comcode\Tests;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Subjects\ClassSubject;
use ErrorException;
use JetBrains\PhpStorm\NoReturn;
use Traversable;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function assertClassContains(
        string|array $needle,
        ?ClassSubject $subject = null
    ) {
        $subject = $subject ?: $this->class();
        foreach ((array) $needle as $item) {
            if (! str_contains($subject, $item)) {
                $this->fail($subject.' = '.$item);
            }
        }
    }

    protected function class(): ClassSubject
    {
        $class = php()->class(\Tests\TestedClass::class);
        $class->use(Comcode::class);
        $class->use(Traversable::class);

        $class->extends('Comcode');
        $class->implement('Traversable');
        return $class;
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
