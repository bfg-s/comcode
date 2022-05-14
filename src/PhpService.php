<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Exceptions\CodeNotFoundException;
use Bfg\Comcode\Exceptions\PhpParserErrorException;
use Bfg\Comcode\Subjects\ClassSubject;
use Bfg\Comcode\Subjects\FileSubject;
use PhpParser\Error;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use ReflectionClass;

class PhpService
{
    /**
     * @param  string  $file
     * @return FileSubject
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
     * @param  string  $code
     * @return Stmt[]|null
     * @throws CodeNotFoundException
     * @throws PhpParserErrorException
     */
    public static function parse(string $code): ?array
    {
        if (
            !str_contains($code, "\n")
            && str_contains($code, DIRECTORY_SEPARATOR)
        ) {
            if (
                !is_file($code)
                && is_file(base_path($code))
            ) {
                $code = base_path($code);
            }

            if (
                !is_file($code)
                && is_file(base_path($code.'.php'))
            ) {
                $code = base_path($code.'.php');
            }

            if (is_file($code)) {
                $code = file_get_contents($code);
            }
        }

        if (!$code) {
            throw new CodeNotFoundException();
        }

        if (!str_starts_with($code, "<?php")) {
            $code = "<?php\n".$code;
        }

        try {
            return (new ParserFactory())
                ->create(ParserFactory::PREFER_PHP7)
                ->parse($code);
        } catch (Error $exception) {
            throw new PhpParserErrorException(
                message: $exception->getMessage(),
                previous: $exception
            );
        }
    }

    /**
     * @param  array|Expr  $stmts
     * @return string
     */
    public static function print(array|Expr $stmts): string
    {
        return (new Standard)
            ->{is_array($stmts) ? 'prettyPrintFile' : 'prettyPrintExpr'}($stmts);
    }
}
