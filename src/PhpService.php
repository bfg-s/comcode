<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Subjects\ClassSubject;
use Bfg\Comcode\Subjects\FileSubject;
use ErrorException;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use ReflectionClass;

/**
 * @property-read Stmt $null
 */
class PhpService
{
    public static function boot(): void
    {
        if (
            (! defined('APP_ENV') || APP_ENV !== 'testing')
            && (\PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg')
        ) {
            register_shutdown_function([FixStandard::class, 'standardTouched']);
        }
    }

    /**
     * @param  object|string  $class
     * @param  string|null  $file
     * @return ClassSubject
     * @throws ErrorException
     */
    public function class(object|string $class, string $file = null): ClassSubject
    {
        $file = $file ?: Comcode::fileReservation(
            str_replace(
                "\\",
                DIRECTORY_SEPARATOR,
                lcfirst(trim($class, "\\"))
            ).'.php'
        );

        if ((!$file || !is_file($file)) && class_exists($class)) {
            $file = (new ReflectionClass($class))->getFileName();
        }

        return $this->file($file)
            ->class($class);
    }

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
     * @return InlineTrap
     */
    public function this(): InlineTrap
    {
        return $this->var('this');
    }

    /**
     * @param  string|Expr  $name
     * @return InlineTrap
     */
    public function var(
        string|Expr $name
    ): InlineTrap {
        return new InlineTrap($name);
    }

    /**
     * @param  string  $function
     * @param ...$arguments
     * @return InlineTrap
     */
    public function func(
        string $function,
        ...$arguments
    ): InlineTrap {
        return $this->var(
            Node::callFunction($function, ...$arguments)
        );
    }

    /**
     * @param  mixed|null  $value
     * @return Expr|null
     */
    public function real(
        mixed $value = null
    ): ?Expr {
        return Comcode::defineValueNode($value);
    }

    /**
     * @param  string  $name
     * @return Expr|null
     */
    public function __get(string $name)
    {
        if ($name == 'null') {
            return Comcode::defineValueNode(null);
        }
        return null;
    }
}
