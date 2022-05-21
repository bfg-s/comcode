<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Subjects\SubjectAbstract;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

class Comcode
{
    /**
     * @param  string  $file
     * @return array|null
     */
    public static function parsPhpFile(string $file): ?array
    {
        return (new ParserFactory())
            ->create(ParserFactory::PREFER_PHP7)
            ->parse(
                file_get_contents($file)
            );
    }

    public static function printStmt($node): string
    {
        return (new Standard)
            ->prettyPrint(is_array($node) ? $node : [$node]);
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
        if (! is_file($file)) {
            foreach ($sources as $source) {
                $newFile = $source ? $source($file) : __DIR__ . '/' . $file;
                if (is_file($newFile)) {
                    return $newFile;
                }
            }
            return null;
        }
        return ! str_contains($file, DIRECTORY_SEPARATOR)
            ? getcwd() . '/' . $file
            : $file;
    }

    /**
     * @param  string  $file
     * @return string
     */
    public static function fileReservation(string $file): string
    {
        $prefix = getcwd() . '/';

        if (! str_starts_with($file, $prefix)) {

            $file = $prefix . $file;
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
     * @param  Stmt[]  $nodes
     * @param  string  $is_a
     * @param  callable|null  $clarificationCallback
     * @return Stmt|void|null
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
            if ($node instanceof QueryNodeBuilder) {
                $node = $node->stmt;
            }
            if (property_exists($node, 'stmts')) {
                $node->stmts = static::undressNodes($node->stmts);
            }
            $result[] = $node;
        }
        return $result;
    }

    /**
     * @param  array  $stmts
     * @return AnonymousStmt
     */
    public static function anonymousStmt(array $stmts = []): AnonymousStmt
    {
        return new AnonymousStmt($stmts);
    }
}
