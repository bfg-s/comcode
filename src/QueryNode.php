<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Interfaces\AlwaysLastNodeInterface;
use Bfg\Comcode\Interfaces\AnonymousInterface;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\Nodes\LineNode;
use Bfg\Comcode\Nodes\RowNode;
use Bfg\Comcode\Subjects\DocSubject;
use Bfg\Comcode\Subjects\SubjectAbstract;
use Bfg\Comcode\Traits\Conditionable;
use PhpParser\NodeAbstract;

abstract class QueryNode
{
    use Conditionable;

    /**
     * @var NodeAbstract|null
     */
    public ?NodeAbstract $node = null;

    /**
     * @var NodeAbstract|null
     */
    public ?NodeAbstract $original = null;

    /**
     * @var QueryNode
     */
    public ?QueryNode $parent = null;

    /**
     * @var bool
     */
    public bool $prepend = false;

    /**
     * @var string
     */
    public string $store = 'stmts';

    /**
     * @var SubjectAbstract
     */
    public SubjectAbstract $subject;

    /**
     * @var int
     */
    public int $rowCount = 0;

    /**
     * @return bool
     */
    public static function modified(): bool
    {
        return false;
    }

    /**
     * @param  string  $name
     * @return RowNode
     */
    public function line(
        ?int $num = null
    ): LineNode {
        return $this->apply(
            new LineNode($num === null ? $this->rowCount++ : $num)
        );
    }

    /**
     * Create new query node content
     * @template QUERY_NODE
     * @param  QUERY_NODE|QueryNode  $nodeClass
     * @return QUERY_NODE
     */
    public function apply(
        QueryNode $nodeClass
    ): QueryNode {
        $nodeClass->parent = $this;

        $nodeClass->subject = $this->subject;

        $store = $nodeClass->store;

        $query = Query::new($this->node->{$store})
            ->unless(
                $nodeClass instanceof AnonymousInterface,
                fn(Query $query) => $query
                    ->isA($nodeClass->nodeClass())
            )->when(
                $nodeClass instanceof ClarificationNodeInterface,
                fn(Query $query) => $query
                    ->filter([$nodeClass, 'clarification'])
            );

        $key = $query->firstKey();

        $nodeClass->node = $query->first();

        $nodeClass->original = clone $nodeClass->node;

        $nodeClass->mounting();

        $nodeClass->isMatch()
            ? $nodeClass instanceof ReconstructionNodeInterface && $nodeClass->reconstruction()
            : $nodeClass instanceof BirthNodeInterface && $nodeClass->node = $nodeClass->birth();

        if (property_exists($this->node, $store)) {
            if (is_array($this->node->{$store})) {
                if (is_int($key)) {
                    if ($nodeClass instanceof AlwaysLastNodeInterface) {
                        $this->node->{$store}[] = $nodeClass->node;
                        unset($this->node->{$store}[$key]);
                    } else {
                        $this->node->{$store}[$key] = $nodeClass->node;
                    }
                } else {
                    if (
                        $nodeClass instanceof RowNode
                        && $this->rowCount
                    ) {
                        $this->node->{$store} = $this->moveBefore(
                            $this->node->{$store}, $this->rowCount - 1, $nodeClass->node
                        );
                    } else {
                        if ($nodeClass->prepend) {
                            array_unshift($this->node->{$store}, $nodeClass->node);
                        } else {
                            $this->node->{$store}[] = $nodeClass->node;
                        }
                    }
                }
            } else {
                $this->node->{$store} = $nodeClass->node;
            }
        }

        $nodeClass->mounted();

        return $nodeClass;
    }

    /**
     * @param ...$arguments
     * @return static
     */
    public static function new(
        ...$arguments
    ): static {
        return new static(...$arguments);
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    abstract public function nodeClass(): string;

    /**
     * @return void
     */
    public function mounting(): void
    {
    }

    /**
     * @return bool
     */
    public function isMatch(): bool
    {
        return $this->node && is_a($this->node, $this->nodeClass());
    }

    /**
     * Change the order of an associated array, moving by key before another
     *
     * @param  array  $items
     * @param  int  $key
     * @param  mixed  $value
     * @return array
     */
    protected function moveBefore(
        array $items,
        int $key,
        mixed $value
    ): array {
        $key = max($key, 0);
        $count = count($items);
        $lastKey = array_key_last($items);
        if (!$count) {
            $items[] = $value;
            return $items;
        }
        if (!is_null($lastKey) && $lastKey < $key) {
            $items[$key] = $value;
            return $items;
        }
        $result = [];
        $iterations = 0;
        foreach ($items as $itemKey => $item) {
            if (
                $iterations >= $key
                && $itemKey <= $key
                && $value
            ) {
                $result[] = $value;
                $value = null;
            }
            $result[] = $item;
            $iterations++;
        }
        return $result;
    }

    /**
     * @return void
     */
    public function mounted(): void
    {
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function forgetLine(
        ?int $num = null
    ): bool {
        return $this->forget(
            new LineNode($num === null ? $this->rowCount-- : $num)
        );
    }

    /**
     * @param  QueryNode  $nodeClass
     * @return bool
     */
    public function forget(
        QueryNode $nodeClass
    ): bool {
        $store = $nodeClass->store;
        $nodeClass->subject = $this->subject;
        $query = Query::find($this->node, $nodeClass);
        $key = $query->firstKey();

        if (property_exists($this->node, $store)) {
            if (is_array($this->node->{$store})) {
                if (is_int($key)) {
                    $arr = $this->node->{$store};
                    unset($arr[$key]);
                    $this->node->{$store} = array_values($arr);
                    return true;
                }
            } else {
                $this->node->{$store} = null;
                return true;
            }
        }
        return false;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function notExistsLine(
        ?int $num = null
    ): bool {
        return !$this->existsLine($num);
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function existsLine(
        ?int $num = null
    ): bool {
        return $this->exists(
            new LineNode($num === null ? $this->rowCount : $num)
        );
    }

    /**
     * @param  QueryNode  $nodeClass
     * @return bool
     */
    public function exists(
        QueryNode $nodeClass
    ): bool {
        $nodeClass->subject = $this->subject;
        return Query::find($this->node, $nodeClass)
            ->isNotEmpty();
    }

    /**
     * @param  string  $name
     * @return RowNode
     */
    public function row(
        string $name
    ): RowNode {
        $this->rowCount++;
        return $this->apply(
            new RowNode($name)
        );
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function forgetRow(
        string $name
    ): bool {
        $this->rowCount--;
        return $this->forget(
            new RowNode($name)
        );
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function notExistsRow(
        string $name
    ): bool {
        return !$this->existsRow($name);
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function existsRow(
        string $name
    ): bool {
        return $this->exists(
            new RowNode($name)
        );
    }

    /**
     * @param  string|callable  $text
     * @return $this
     */
    public function comment(
        string|callable $text
    ): static {
        if (is_callable($text)) {
            $comment = $this->node?->getDocComment();
            $doc = new DocSubject($this->subject);
            call_user_func($text, $doc, $comment, $this);
            $text = $doc->render();
        }

        $this->node?->setDocComment(
            Node::doc((string) $text)
        );

        return $this;
    }

    /**
     * @return bool
     */
    public function notExistsComment(): bool
    {
        return !$this->existsComment();
    }

    /**
     * @return bool
     */
    public function existsComment(): bool
    {
        return (bool) $this->node?->getDocComment();
    }

    /**
     * @param  string  $name
     * @return null
     */
    public function __get(
        string $name
    ) {
        return $this->node?->{$name};
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->node;
    }
}
