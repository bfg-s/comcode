<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Subjects\AnonymousClassSubject;
use Bfg\Comcode\Subjects\ClassSubject;
use Bfg\Comcode\Subjects\EnumSubject;
use Bfg\Comcode\Subjects\FileSubject;
use Bfg\Comcode\Subjects\InterfaceSubject;
use Bfg\Comcode\Subjects\TraitSubject;
use ErrorException;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use ReflectionClass;

/**
 * @property-read Stmt $null
 */
class PhpService
{
    /**
     * @param  string  $class
     * @param  string|null  $file
     * @return ClassSubject
     * @throws ErrorException
     */
    public function class(
        string $class,
        string $file = null
    ): ClassSubject {
        $file = $file ?: str_replace(
            "\\",
            DIRECTORY_SEPARATOR,
            lcfirst(trim($class, "\\"))
        ).'.php';

        if ((!$file || !is_file($file)) && class_exists($class)) {
            $file = (new ReflectionClass($class))->getFileName();
        }

        return $this->file(Comcode::fileReservation($file))
            ->class($class);
    }

    /**
     * @param  string  $class
     * @param  string|null  $file
     * @return TraitSubject
     * @throws ErrorException
     */
    public function trait(
        string $class,
        string $file = null
    ): TraitSubject {
        $file = $file ?: str_replace(
            "\\",
            DIRECTORY_SEPARATOR,
            lcfirst(trim($class, "\\"))
        ).'.php';

        if ((!$file || !is_file($file)) && trait_exists($class)) {
            $file = (new ReflectionClass($class))->getFileName();
        }

        return $this->file(Comcode::fileReservation($file))
            ->trait($class);
    }

    /**
     * @param  string  $class
     * @param  string|null  $file
     * @return EnumSubject
     * @throws ErrorException
     */
    public function enum(
        string $class,
        string $file = null,
    ): EnumSubject {

        $file = $file ?: str_replace(
            "\\",
            DIRECTORY_SEPARATOR,
            lcfirst(trim($class, "\\"))
        ).'.php';

        if ((!$file || !is_file($file)) && trait_exists($class)) {
            $file = (new ReflectionClass($class))->getFileName();
        }

        return $this->file(Comcode::fileReservation($file))
            ->enum($class);
    }

    /**
     * @param  string  $class
     * @param  string|null  $file
     * @return InterfaceSubject
     * @throws ErrorException
     */
    public function interface(
        string $class,
        string $file = null
    ): InterfaceSubject {
        $file = $file ?: str_replace(
            "\\",
            DIRECTORY_SEPARATOR,
            lcfirst(trim($class, "\\"))
        ).'.php';

        if ((!$file || !is_file($file)) && interface_exists($class)) {
            $file = (new ReflectionClass($class))->getFileName();
        }

        return $this->file(Comcode::fileReservation($file))
            ->interface($class);
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
     * @param  string|null  $namespace
     * @param  string  $file
     * @return AnonymousClassSubject
     * @throws ErrorException
     */
    public function anonymousClass(
        string $file,
        ?string $namespace = null,
    ): AnonymousClassSubject {
        return $this->file($file)
            ->anonymousClass($namespace);
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
     * @param  string  $raw
     * @return Expr|null
     */
    public function raw(
        string $raw
    ): ?Expr {
        return Comcode::anonymousLine($raw);
    }

    /**
     * @param  string  $raw
     * @return string
     */
    public function rawForArray(
        string $raw
    ): string {
        return "RAW($raw)";
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
