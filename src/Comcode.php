<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Subjects\ClassSubject;
use Closure;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeAbstract;
use PhpParser\ParserFactory;
use PhpParser\{Lexer, NodeTraverser, NodeVisitor, Parser, PhpVersion};

class Comcode
{
    /**
     * @var array
     */
    public static array $defaultClassList = [];

    protected static array $tokens = [];

    /**
     * @var callable[]|Closure[]
     */
    protected static array $_callbacks = [];

    /**
     * @param  string  $name
     * @param  Closure|callable  $callback
     * @return void
     */
    public static function on(string $name, Closure|callable $callback): void
    {
        static::$_callbacks[$name] = $callback;
    }

    /**
     * @param  string  $name
     * @param ...$arguments
     * @return mixed
     */
    public static function emit(string $name, ...$arguments): mixed
    {
        if (array_key_exists($name, static::$_callbacks)) {
            return call_user_func_array(static::$_callbacks[$name], $arguments);
        }
        return null;
    }

    /**
     * @param  string  $file
     * @return array|null
     */
    public static function parsPhpFile(
        string $file
    ): ?array {
        return static::parsPhp(
            file_get_contents($file),
            $file
        );
    }

    /**
     * @param  string  $code
     * @param  string|null  $file
     * @return array|null
     */
    public static function parsPhp(
        string $code,
        ?string $file = null,
    ): ?array {
        $code = str_starts_with($code, "<?php")
            ? $code : "<?php\n\n".$code;

        if ($file) {
            $phpVersion = PhpVersion::fromComponents(8, 4);
            $lexer = new Lexer\Emulative($phpVersion);
            $parser = new Parser\Php7($lexer);

            $traverser = new NodeTraverser();
            $traverser->addVisitor(new NodeVisitor\CloningVisitor());

            $oldStmts = $parser->parse($code);

            static::$tokens[$file]
                = [$oldStmts, $lexer, $parser->getTokens()];

            return $traverser->traverse($oldStmts);
        }
        return (new ParserFactory())
            ->create(ParserFactory::PREFER_PHP7)
            ->parse($code);
    }

    /**
     * @param $node
     * @param  string|null  $file
     * @return string
     */
    public static function printStmt(
        $node,
        ?string $file = null
    ): string {
        if ($file && isset(static::$tokens[$file])) {
            return (new PrettyPrinter)
                ->printFormatPreserving(
                    $node,
                    static::$tokens[$file][0],
                    static::$tokens[$file][2],
                );
        }

        return (new PrettyPrinter)
            ->prettyPrint(
                Comcode::undressNodes(
                    is_array($node) ? $node : [$node]
                )
            );
    }

    /**
     * @param  array  $nodes
     * @return array
     */
    public static function undressNodes(
        array $nodes = []
    ): array {
        $result = [];

        foreach ($nodes as $node) {
            if (
                $node instanceof QueryNode
                || $node instanceof InlineTrap
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
     * @param  string  $file
     * @return string
     */
    public static function fileReservation(
        string $file
    ): string {
        $prefix = base_path().DIRECTORY_SEPARATOR;

        if (!str_starts_with($file, $prefix)) {
            $file = $prefix.$file;
        }

        Comcode::emit('file-reservation', $file);

        if ($findFile = static::findFile($file)) {
            return $findFile;
        }

        $dir = dirname($file);

        if (! is_dir($dir)) {

            mkdir($dir, 0777, true);
        }

        file_put_contents($file, "<?php\n\n");

        return $file;
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
     * @param  string  $class
     * @return string
     */
    public static function namespaceBasename(
        string $class
    ): string {
        return implode(
            "\\", array_slice(explode("\\", $class), 0, -1)
        );
    }

    /**
     * @param  string|Expr|null  $expr
     * @return AnonymousExpr
     */
    public static function anonymousExpr(
        string|Expr|null $expr = null
    ): AnonymousExpr {
        return new AnonymousExpr(
            func_num_args() == 0 && is_null($expr)
                ? static::anonymousStmt()
                : $expr
        );
    }

    /**
     * @param  array  $nodes
     * @return AnonymousStmt
     */
    public static function anonymousStmt(
        array $nodes = []
    ): AnonymousStmt {
        return new AnonymousStmt($nodes);
    }

    /**
     * @param  string|Expr|null  $expr
     * @return AnonymousLine
     */
    public static function anonymousLine(
        string|Expr|null $expr = null
    ): AnonymousLine {
        return new AnonymousLine(
            func_num_args() == 0
                ? static::anonymousStmt()
                : $expr
        );
    }

    /**
     * @param  mixed|null  $value
     * @param  bool|int  $inline
     * @return Expr|null
     */
    public static function defineValueNode(
        mixed $value = null,
        bool|int $inline = 4
    ): ?Expr {
        if ($value instanceof Expr) {
            return $value;
        }
        if (is_int($value)) {
            return new LNumber($value);
        } else {
            if (is_float($value)) {
                return new DNumber($value);
            }
        }
        if (
            is_string($value)
            && (static::isCanBeClass($value) || str_ends_with($value, '::class'))
        ) {
            $value = str_replace('::class', '', $value);
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
                            $parser = (new ParserFactory)->createForVersion(PhpVersion::fromComponents(8,4));
                            $result = $parser->parse("<?php\n\n\$variable = ".static::var_export($value, $inline).';');
                            return ($result[0] ?? null)?->expr?->expr;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param  mixed  $class
     * @return bool
     */
    public static function isCanBeClass(
        mixed $class
    ): bool {
        return $class && (preg_match('/^\\\\?(\w+\\\\+\w+)+$/', $class)
                || static::isDefaultClass($class));
    }

    /**
     * @param  string  $class
     * @return bool
     */
    public static function isDefaultClass(
        string $class
    ): bool {
        return in_array($class, static::defaultClassList());
    }

    /**
     * @return array
     */
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

    /**
     * @param $var
     * @param  bool|int  $inline
     * @param $indent
     * @return string|null
     */
    public static function var_export($var, bool|int $inline = 4, $indent = ""): ?string
    {
        switch (gettype($var)) {
            case "string":
                if (
                    preg_match('/^RAW\((.*)\)$/', $var, $m)
                ) {
                    return $m[1];
                }
                if (str_ends_with($var, '::class')) {
                    return $var;
                }
                if (Comcode::isCanBeClass($var)) {
                    return $var.'::class';
                }
                return '"'.addcslashes($var, "\\\$\"\r\n\t\v\f").'"';
            case "array":
                $inlineOriginal = $inline;
                $count = count($var);
                $inline = !is_bool($inline) ? $count < $inline : $inline;
                $eol = !$inline ? PHP_EOL : "";
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = ($inline ? '' : "$indent    ")
                        .($indexed ? "" : static::var_export($key, $inlineOriginal)." => ")
                        .static::var_export($value, $inlineOriginal, "$indent    ");
                }
                $e = $inline ? ' ' : '';
                return "[$eol".implode(",$eol{$e}", $r).$eol.($inline ? '' : $indent)."]";
            case "boolean":
                return $var ? "true" : "false";
            case "NULL":
                return "null";
            default:
                return var_export($var, true);
        }
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

    /**
     * @param  NodeAbstract|array  $node
     * @param  string  $name
     * @param  string|null  $is_a
     * @return array|mixed|NodeAbstract|null
     */
    public static function findStmtByName(
        NodeAbstract|array $node,
        string $name,
        string $is_a = null,
    ): mixed {
        $alreadyList = is_array($node);
        $list = $alreadyList ? $node : [];

        if (
            !$alreadyList
            && property_exists($node, 'stmts')
        ) {
            $alreadyList = true;
            $list = $node->stmts;
        }

        if (!$alreadyList) {
            $list[] = $node;
        }

        foreach ($list as $item) {
            if (
                (!$is_a || is_a($item, $is_a))
                && property_exists($item, 'name')
                && (string) $item->name == $name
            ) {
                return $item;
            } else {
                if (
                    property_exists($item, 'expr')
                    && $result = static::findStmtByName($item->expr, $name)
                ) {
                    return $result;
                } else {
                    if (
                        property_exists($item, 'var')
                        && $result = static::findStmtByName($item->var, $name)
                    ) {
                        return $result;
                    } else {
                        if (
                            property_exists($item, 'class')
                            && (string) $item->class == $name
                        ) {
                            return $item;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param  NodeAbstract|null  $node
     * @return int
     */
    public static function maxInlineInner(
        ?NodeAbstract $node = null,
    ): int {
        if ($node instanceof Expression) {
            $node = $node->expr;
        }
        $insideIteration = 0;
        while (
            $node
            && (property_exists($node, 'var') || property_exists($node, 'cl'))
        ) {
            if ($node instanceof Expr\StaticCall) {
                $insideIteration++;
                $node = null;
            } else {
                if (
                    $node instanceof Expr\MethodCall
                    || $node instanceof Expr\PropertyFetch
                ) {
                    $insideIteration++;
                    $node = $node->var;
                } else {
                    $node = null;
                }
            }
        }
        return $insideIteration;
    }

    /**
     * @param  mixed  $name
     * @param  ClassSubject|null  $classSubject
     * @return mixed
     */
    public static function useIfClass(
        mixed $name,
        ?ClassSubject $classSubject = null,
    ): mixed {
        if (
            $classSubject
            && is_string($name)
            && static::isCanBeClass($name)
        ) {
            $classSubject->use($name);

            return static::classBasename($name);
        }

        return $name;
    }

    /**
     * @param $class
     * @return string
     */
    public static function classBasename(
        $class
    ): string {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }

    /**
     * Determine if a given string matches a given pattern.
     *
     * @param  array|string  $pattern
     * @param  string  $value
     * @return bool
     */
    public static function is(array|string $pattern, string $value): bool
    {
        $patterns = (array) $pattern;

        $value = str_replace("\n", ' ', $value);

        if (empty($patterns)) {
            return false;
        }

        foreach ($patterns as $pattern) {
            $pattern = (string) $pattern;

            // If the given value is an exact match we can of course return true right
            // from the beginning. Otherwise, we will translate asterisks and do an
            // actual pattern match against the two strings to see if they match.
            if ($pattern === $value) {
                return true;
            }

            $pattern = preg_quote($pattern, '#');

            // Asterisks are translated into zero-or-more regular expression wildcards
            // to make it convenient to check if the strings starts with the given
            // pattern such as "library/*", making any string check convenient.
            $pattern = str_replace('\*', '.*', $pattern);

            if (preg_match('#^'.$pattern.'\z#um', $value) === 1) {
                return true;
            }
        }

        return false;
    }
}
