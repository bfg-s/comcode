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

    protected ?Stmt $stmt = null;
    protected ?string $stmtStore = null;

    public function __construct(
        string|Expr $var,
        public array $nodes = [],
        array $attributes = []
    ) {
        parent::__construct($this->nodes, $attributes);

        $this->node = is_string($var)
            ? Node::var($var)
            : $var;
        $this->nodes[] = $this->node;
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

    public function call(
        string $method,
        ...$arguments
    ): static {
        $this->node = Node::callMethod(
            $this->node,
            $method,
            ...$arguments
        );
        $this->__setToStmt();
        return $this;
    }

    public function get(
        string $property
    ): static {
        $this->node = Node::callProperty(
            $this->node,
            $property
        );
        $this->__setToStmt();
        return $this;
    }

    public function __call(string $name, array $arguments)
    {
        return $this->call($name, ...$arguments);
    }

    public function __get(string $property)
    {
        return $this->get($property);
    }
}
