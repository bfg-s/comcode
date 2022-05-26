<?php

namespace Bfg\Comcode;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

/**
 * @internal
 */
class PhpInlineTrap extends AnonymousStmt
{
    /**
     * @var Expr|null
     */
    public ?Expr $node;
    public ?Expr $firstNode = null;

    protected ?Stmt $stmt = null;
    protected ?string $stmtStore = null;

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

    public function assign(
        string|Expr|PhpInlineTrap $expr
    ): static {
        $this->__assignNode($expr, 'assign');
        return $this;
    }

    protected function __assignNode(
        string|Expr|PhpInlineTrap $expr,
        string $mode
    ): void {
        if ($expr instanceof PhpInlineTrap) {
            $expr = $expr->node;
        }
        $expr = is_string($expr)
            ? php()->var($expr)->node
            : $expr;
        $this->node = call_user_func(
            [Node::class, $mode],
            $this->node,
            $expr
        );
        $this->__setToStmt();
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

    public function concat(
        string|Expr|PhpInlineTrap $expr
    ): static {
        $this->__assignNode($expr, 'concat');
        return $this;
    }

    public function plus(
        string|Expr|PhpInlineTrap $expr
    ): static {
        $this->__assignNode($expr, 'plus');
        return $this;
    }

    public function minus(
        string|Expr|PhpInlineTrap $expr
    ): static {
        $this->__assignNode($expr, 'minus');
        return $this;
    }

    public function func(
        string $function,
        ...$arguments
    ): static {
        return $this->__call($function, $arguments);
    }

    public function __call(
        string $name,
        array $arguments
    ) {
        $this->node = Node::callMethod(
            $this->node,
            $name,
            ...$arguments
        );
        $this->__setToStmt();
        return $this;
    }

    public function prop(
        string $property
    ): static {
        return $this->__get($property);
    }

    public function __get(
        string $property
    ) {
        $this->node = Node::callProperty(
            $this->node,
            $property
        );
        $this->__setToStmt();
        return $this;
    }

    /**
     * @param  Stmt  $stmt
     * @param  string  $store
     * @return $this
     * @internal
     */
    public function __bindExpression(
        Stmt $stmt,
        string $store = 'expr'
    ): static {
        $this->stmt = $stmt;
        $this->stmtStore = $store;
        return $this;
    }
}
