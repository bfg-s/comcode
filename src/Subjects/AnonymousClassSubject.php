<?php

namespace Bfg\Comcode\Subjects;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Node;
use Bfg\Comcode\Nodes\AnonymousClassNode;
use Bfg\Comcode\Nodes\ExpressionNode;
use Bfg\Comcode\Nodes\NamespaceNode;
use Bfg\Comcode\Nodes\NamespaceUseNode;
use Bfg\Comcode\Nodes\ReturnNode;
use PhpParser\Node\Expr\New_;

class AnonymousClassSubject extends ClassSubject
{
    /**
     * @param  string  $namespace
     * @return NamespaceUseNode
     */
    public function use(
        string $namespace
    ): NamespaceUseNode {
        return $this->apply(
            new NamespaceUseNode($namespace)
        );
    }

    /**
     * Discover individual environment
     * @return void
     */
    protected function discoverStmtEnvironment(): void
    {
        $next = $this;

        if ($this->class) {
            $next = $this->namespaceNode = $this->apply(
                new NamespaceNode($this->class."\\AnonymousClass")
            );
        }

        $return = $next->apply(
            new ReturnNode()
        );

        $new = $return->apply(
            new ExpressionNode(
                New_::class,
                'expr',
                ['class' => Comcode::anonymousLine()]
            )
        );

        $this->classNode = $new->apply(
            new AnonymousClassNode()
        );
    }
}
