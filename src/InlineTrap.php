<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Nodes\ClosureNode;
use Bfg\Comcode\Traits\Conditionable;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\NodeAbstract;

class InlineTrap extends AnonymousStmt
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
        parent::__construct($this->nodes, $attributes);

        $this->node = $this->firstNode = is_string($var)
            ? Node::var($var)
            : $var;
        $this->nodes[] = $this->node;
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
        if ($expr instanceof InlineTrap) {
            $current = $expr;
            $expr = $current->node;
        }
        if (is_string($expr)) {
            $current = (new InlineTrap($expr));
            $expr = $current->node;
        }
        $this->node = call_user_func(
            [Node::class, $mode],
            $this->node,
            $expr,
        );
        $this->__setToStmt();
        return $current->__bindExpression($this->queryNode, $this->node);
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
        if (str_ends_with($name, '::')) {
            $name = substr($name, 0, -2);
            $this->node = Node::callStaticMethod(
                $name,
                $this->node instanceof Variable
                    ? (string) $this->node->name
                    : $this->node,
            );
            $static = true;
        } else {
            $this->node = Node::callMethod(
                $this->node,
                $name,
            );
        }

        foreach ($arguments as $key => $argument) {
            if (is_callable($argument) && ! is_string($argument)) {
                /** @var Expr\MethodCall|null $searchResult */
                $searchResult = Comcode::findStmtByName(
                    $this->queryNode->original,
                    $name,
                    $this->node::class
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

        $this->node->args
            = Node::args($arguments);
        $this->__setToStmt();
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
        $this->node = Node::callProperty(
            $this->node,
            $property
        );
        $this->__setToStmt();
        $this->iterations++;
        return $this;
    }
}
