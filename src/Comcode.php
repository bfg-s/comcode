<?php

namespace Bfg\Comcode;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\Node\Scalar\LNumber;

class Comcode
{
    public static array $defaultClassList = [];

    /**
     * @param  string  $code
     * @return array|null
     */
    public static function parsPhp(string $code): ?array
    {
        $code = str_starts_with($code, "<?php")
            ? $code : "<?php\n\n".$code;
        return (new ParserFactory())
            ->create(ParserFactory::PREFER_PHP7)
            ->parse($code);
    }

    /**
     * @param  string  $file
     * @return array|null
     */
    public static function parsPhpFile(string $file): ?array
    {
        return static::parsPhp(
            file_get_contents($file)
        );
    }

    public static function printStmt($node, bool $file = false): string
    {
        return (new PrettyPrinter)
            ->{$file ? 'prettyPrintFile' : 'prettyPrint'}(
                Comcode::undressNodes(
                    is_array($node) ? $node : [$node]
                )
            );
    }

    /**
     * @param  string  $file
     * @param  array  $sources
     * @return string|null
     */
    public static function findFile(
        string $file,
        array $sources = [null, 'base_path']
    ): ?string {
        if (!is_file($file)) {
            foreach ($sources as $source) {
                $newFile = $source ? $source($file) : __DIR__.'/'.$file;
                if (is_file($newFile)) {
                    return $newFile;
                }
            }
            return null;
        }
        return !str_contains($file, DIRECTORY_SEPARATOR)
            ? getcwd().'/'.$file
            : $file;
    }

    /**
     * @param  string  $file
     * @return string
     */
    public static function fileReservation(string $file): string
    {
        $prefix = getcwd().'/';

        if (!str_starts_with($file, $prefix)) {
            $file = $prefix.$file;
        }

        if ($findFile = static::findFile($file)) {
            return $findFile;
        }

        file_put_contents($file, "<?php\n\n");

        return $file;
    }

    /**
     * @param  string  $class
     * @return string
     */
    public static function namespaceBasename(string $class): string
    {
        return implode(
            "\\", array_slice(explode("\\", $class), 0, -1)
        );
    }

    /**
     * @param $class
     * @return string
     */
    public static function classBasename($class): string
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }

    /**
     * @param  NodeAbstract[]  $nodes
     * @param  string  $is_a
     * @param  callable|null  $clarificationCallback
     * @return NodeAbstract|void|null
     */
    public static function detectNode(
        array $nodes,
        string $is_a,
        ?callable $clarificationCallback = null
    ) {
        foreach ($nodes as $node) {
            $test = !$clarificationCallback
                || call_user_func($clarificationCallback, $node);

            if (is_a($node, $is_a) && $test) {
                return $node;
            }
        }
    }

    /**
     * @param  array  $nodes
     * @return array
     */
    public static function undressNodes(array $nodes = []): array
    {
        $result = [];

        foreach ($nodes as $node) {
            if (
                $node instanceof QueryNode
                || $node instanceof PhpInlineTrap
            ) {
                $node = $node->node;
            }
            if (property_exists($node, 'stmts')) {
                $node->stmts = static::undressNodes($node->stmts);
            }
            $result[] = $node;
        }
        return $result;
    }

    /**
     * @param  array  $nodes
     * @return AnonymousStmt
     */
    public static function anonymousStmt(array $nodes = []): AnonymousStmt
    {
        return new AnonymousStmt($nodes);
    }

    public static function defaultClassList(): array
    {
        if (!static::$defaultClassList) {
            static::$defaultClassList = array_values(array_merge(
                spl_classes(),
                get_declared_classes(),
                get_declared_interfaces(),
                get_declared_traits(),
            ));
        }

        return static::$defaultClassList;
    }

    public static function isDefaultClass(string $class): bool
    {
        return in_array($class, static::defaultClassList());
    }

    public static function isCanBeClass(string $class): bool
    {
        return preg_match('/^\\\\?(\w+\\\\+\w+)+$/', $class)
            || static::isDefaultClass($class);
    }

    /**
     * @param  mixed|null  $value
     * @param  bool|int  $inline
     * @return Expr|null
     */
    public static function defineValueNode(mixed $value = null, bool|int $inline = 4): ?Expr
    {
        if ($value instanceof Expr) {
            return $value;
        }
        if (
            is_string($value)
            && static::isCanBeClass($value)
        ) {
            return new ClassConstFetch(
                Node::name($value),
                'class'
            );
        } else {
            if (is_string($value)) {
                return new String_($value);
            } else {
                if (is_bool($value)) {
                    return new ConstFetch(
                        Node::name($value ? 'true' : 'false')
                    );
                } else {
                    if (is_null($value) && func_num_args() === 1) {
                        return new ConstFetch(
                            Node::name('null')
                        );
                    } else {
                        if (is_array($value)) {
                            return (Comcode::parsPhp(
                                    "\$variable = ".var_export54($value, $inline).';'
                                )[0] ?? null)?->expr?->expr;
                        } else {
                            if (is_int($value)) {
                                return new LNumber($value);
                            } else {
                                if (is_float($value)) {
                                    return new DNumber($value);
                                }
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param  string  $modifier
     * @param  int  $default
     * @return int
     */
    public static function detectPropertyModifier(
        string $modifier,
        int $default = Class_::MODIFIER_PUBLIC
    ): int {
        return match ($modifier) {
            'public' => Class_::MODIFIER_PUBLIC,
            'public static' => Class_::MODIFIER_PUBLIC | Class_::MODIFIER_STATIC,
            'protected' => Class_::MODIFIER_PROTECTED,
            'protected static' => Class_::MODIFIER_PROTECTED | Class_::MODIFIER_STATIC,
            'private' => Class_::MODIFIER_PRIVATE,

            'abstract public' => Class_::MODIFIER_PUBLIC | Class_::MODIFIER_ABSTRACT,
            'abstract public static' => Class_::MODIFIER_PUBLIC | Class_::MODIFIER_STATIC | Class_::MODIFIER_ABSTRACT,
            'abstract protected' => Class_::MODIFIER_PROTECTED | Class_::MODIFIER_ABSTRACT,
            'abstract protected static' => Class_::MODIFIER_PROTECTED | Class_::MODIFIER_STATIC | Class_::MODIFIER_ABSTRACT,
            'abstract private' => Class_::MODIFIER_PRIVATE | Class_::MODIFIER_ABSTRACT,

            'final public' => Class_::MODIFIER_PUBLIC | Class_::MODIFIER_FINAL,
            'final public static' => Class_::MODIFIER_PUBLIC | Class_::MODIFIER_STATIC | Class_::MODIFIER_FINAL,
            'final protected' => Class_::MODIFIER_PROTECTED | Class_::MODIFIER_FINAL,
            'final protected static' => Class_::MODIFIER_PROTECTED | Class_::MODIFIER_STATIC | Class_::MODIFIER_FINAL,
            'final private' => Class_::MODIFIER_PRIVATE | Class_::MODIFIER_FINAL,

            'abstract final public' => Class_::MODIFIER_PUBLIC | Class_::MODIFIER_ABSTRACT | Class_::MODIFIER_FINAL,
            'abstract final public static' => Class_::MODIFIER_PUBLIC | Class_::MODIFIER_STATIC | Class_::MODIFIER_ABSTRACT | Class_::MODIFIER_FINAL,
            'abstract final protected' => Class_::MODIFIER_PROTECTED | Class_::MODIFIER_ABSTRACT | Class_::MODIFIER_FINAL,
            'abstract final protected static' => Class_::MODIFIER_PROTECTED | Class_::MODIFIER_STATIC | Class_::MODIFIER_ABSTRACT | Class_::MODIFIER_FINAL,
            'abstract final private' => Class_::MODIFIER_PRIVATE | Class_::MODIFIER_ABSTRACT | Class_::MODIFIER_FINAL,

            default => $default,
        };
    }
}
