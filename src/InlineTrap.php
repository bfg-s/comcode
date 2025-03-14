<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Nodes\ClosureNode;
use Bfg\Comcode\Traits\Conditionable;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeAbstract;

class InlineTrap extends AnonymousExpr
{
    use Conditionable;

    /**
     * @var Expr|null
     */
    public ?Expr $node;

    /**
     * @var Expr|Expr\Variable|null
     */
    public ?Expr $firstNode = null;

    /**
     * @var int
     */
    public int $iterations = 0;

    /**
     * @var QueryNode|null
     */
    public ?QueryNode $queryNode = null;

    /**
     * @var NodeAbstract|null
     */
    protected ?NodeAbstract $stmt = null;

    /**
     * @var string|null
     */
    protected ?string $stmtStore = null;

    /**
     * @param  string|Expr  $var
     * @param  array  $nodes
     * @param  array  $attributes
     */
    public function __construct(
        string|Expr $var,
        public array $nodes = [],
        array $attributes = []
    ) {
        parent::__construct($var, $attributes);

        $this->node = $this->firstNode = is_string($var)
            ? Node::var($var)
            : $var;
    }

    /**
     * @param  string|Expr|InlineTrap  $expr
     * @return $this
     */
    public function assign(
        string|Expr|InlineTrap $expr
    ): static {
        return $this->__assignNode($expr, 'assign');
    }

    /**
     * @param  string|Expr|InlineTrap  $expr
     * @param  string  $mode
     * @return InlineTrap
     */
    protected function __assignNode(
        string|Expr|InlineTrap $expr,
        string $mode
    ): InlineTrap {
        $current = $this;
        if (is_string($expr)) {
            $expr = (new InlineTrap($expr));
            $expr->nodes[] = $expr->node;
            $current = $expr;
        }
        $last = $this->nodes[array_key_last($this->nodes)] ?? $this->node;
        $this->nodes[] = call_user_func(
            [Node::class, $mode],
            $last,
            $expr,
        );

        $current->__bindExpression($this->queryNode, $expr, 'nodes');

        return $current;
    }

    /**
     * @return void
     * @internal
     */
    protected function __setToStmt(): void
    {
        if (
            $this->stmt
            && $this->stmtStore
            && isset($this->stmt->{$this->stmtStore})
        ) {
            if (is_array($this->stmt->{$this->stmtStore})) {
                $this->stmt->{$this->stmtStore}[] = $this->node;
            } else {
                $this->stmt->{$this->stmtStore} = $this->node;
            }
        }
    }

    /**
     * @param  QueryNode  $queryNode
     * @param  Stmt|null  $stmt
     * @param  string  $store
     * @return $this
     * @internal
     */
    public function __bindExpression(
        QueryNode $queryNode,
        NodeAbstract $stmt = null,
        string $store = 'expr'
    ): static {
        $this->stmt = $stmt;
        $this->stmtStore = $store;
        $this->queryNode = $queryNode;
        return $this;
    }

    /**
     * @param  string  $class
     * @param  array  $arguments
     * @return $this
     */
    public function staticCall(
        string $class,
        ...$arguments
    ): static {
        return $this->__call($class.'::', $arguments);
    }

    /**
     * @param  string  $name
     * @param  array  $arguments
     * @return $this
     */
    public function __call(
        string $name,
        array $arguments
    ) {
        $static = false;
        $isReturn = $this?->stmt instanceof Return_;
        $last = $isReturn ? $this->stmt->expr : ($this->nodes[array_key_last($this->nodes)] ?? $this->node);
        if (str_ends_with($name, '::')) {
            $name = substr($name, 0, -2);
            $csm = Node::callStaticMethod(
                $name,
                ($last instanceof Variable
                    ? (string) $last->name
                    : $last) ?: Node::name($this->node->name),
            );
            $static = true;
        } else {
            $csm = Node::callMethod(
                $last,
                $name,
            );
        }
        if ($isReturn) {
            $this->stmt->expr = $csm;
        } else {
            $this->nodes[array_key_last($this->nodes) ?: 0] = $csm;
        }

        foreach ($arguments as $key => $argument) {
            if (is_callable($argument) && ! is_string($argument)) {
                /** @var Expr\MethodCall|null $searchResult */
                $searchResult = Comcode::findStmtByName(
                    $this->queryNode->original,
                    $name,
                    ($isReturn ? $this->stmt->expr : $this->nodes[array_key_last($this->nodes)])::class
                );

                $inn = Comcode::maxInlineInner($searchResult) - $this->iterations - 1;

                for ($i = 0; $i < $inn; $i++) {
                    $searchResult = $searchResult->var;
                }

                $node = null;
                if (
                    $searchResult
                    && isset($searchResult->args[$key])
                    && property_exists($searchResult->args[$key], 'value')
                ) {
                    $class = $searchResult->args[$key]->value;
                    if ($class instanceof Expr\Closure) {
                        $arguments[$key] = $class;
                    } else {
                        $arguments[$key] = Node::closure();
                    }
                } else {
                    $arguments[$key] = Node::closure();
                }
                $node = new ClosureNode($argument);
                $node->mounting();
                $node->parent = $this->queryNode;
                $node->subject = $this->queryNode->subject;
                $node->node = $arguments[$key];
                $node->mounted();
            }
        }

        if ($isReturn) {
            $this->stmt->expr->args = Node::args($arguments);
        } else {
            $this->nodes[array_key_last($this->nodes)]->args = Node::args($arguments);
        }
        $this->iterations++;
        return $this;
    }

    /**
     * @param  string|Expr|InlineTrap  $expr
     * @return $this
     */
    public function concat(
        string|Expr|InlineTrap $expr
    ): static {
        return $this->__assignNode($expr, 'concat');
    }

    /**
     * @param  string|Expr|InlineTrap  $expr
     * @return $this
     */
    public function plus(
        string|Expr|InlineTrap $expr
    ): static {
        return $this->__assignNode($expr, 'plus');
    }

    /**
     * @param  string|Expr|InlineTrap  $expr
     * @return $this
     */
    public function minus(
        string|Expr|InlineTrap $expr
    ): static {
        return $this->__assignNode($expr, 'minus');
    }

    /**
     * @param  string  $function
     * @param ...$arguments
     * @return $this
     */
    public function func(
        string $function,
        ...$arguments
    ): static {
        return $this->__call($function, $arguments);
    }

    /**
     * @param  string  $property
     * @return $this
     */
    public function prop(
        string $property
    ): static {
        return $this->__get($property);
    }

    /**
     * @param  string  $property
     * @return $this
     */
    public function __get(
        string $property
    ) {
        $isReturn = $this?->stmt instanceof Return_;
        $cp = Node::callProperty(
            $isReturn ? $this->stmt->expr : ($this->nodes[array_key_last($this->nodes)] ?? $this->node),
            $property
        );
        if ($isReturn) {
            $this->stmt->expr = $cp;
        } else {
            $this->nodes[array_key_last($this->nodes) ?: 0] = $cp;
        }
        $this->iterations++;
        return $this;
    }
}
