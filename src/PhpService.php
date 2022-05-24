<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Exceptions\CodeNotFound;
use Bfg\Comcode\Exceptions\PhpParserError;
use Bfg\Comcode\Subjects\ClassSubject;
use Bfg\Comcode\Subjects\FileSubject;
use ErrorException;
use PhpParser\Error;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use ReflectionClass;

class PhpService
{
    /**
     * @param  string  $file
     * @return FileSubject
     * @throws ErrorException
     */
    public function file(string $file): FileSubject
    {
        return new FileSubject(
            Comcode::fileReservation($file)
        );
    }

    /**
     * @param  object|string  $class
     * @return ClassSubject
     * @throws ErrorException
     */
    public function class(object|string $class): ClassSubject
    {
        return $this->file(
            class_exists($class)
                ? (new ReflectionClass($class))->getFileName()
                : Comcode::fileReservation(
                str_replace("\\", DIRECTORY_SEPARATOR, lcfirst($class)).'.php')
        )->class($class);
    }

    /**
     * @param  string|Expr  $name
     * @return PhpInlineTrap
     */
    public function var(
        string|Expr $name
    ): PhpInlineTrap {
        return new PhpInlineTrap($name);
    }

    /**
     * @return PhpInlineTrap
     */
    public function this(): PhpInlineTrap
    {
        return $this->var('this');
    }

    public function func(
        string $function,
        ...$arguments
    ): PhpInlineTrap {
        return $this->var(
            Node::callFunction($function, ...$arguments)
        );
    }
}
