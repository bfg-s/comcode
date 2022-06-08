<?php

namespace Bfg\Comcode\Subjects;

use Bfg\Comcode\Nodes\AnonymousClassNode;
use Bfg\Comcode\Nodes\ClassNode;
use Bfg\Comcode\Nodes\ExpressionNode;
use Bfg\Comcode\Nodes\NamespaceNode;
use Bfg\Comcode\Nodes\ReturnNode;

class AnonymousClassSubject extends ClassSubject
{
    /**
     * Discover individual environment
     * @return void
     */
    protected function discoverStmtEnvironment(): void
    {
        $this->namespaceNode = $this->apply(
            new NamespaceNode($this->class . "\\AnonymousClass")
        );

        $return = $this->namespaceNode->apply(
            new ReturnNode()
        );

        $new = $return->apply(
            new ExpressionNode(
                \PhpParser\Node\Expr\New_::class,
                'expr',
                ['class' => null]
            )
        );

        $this->classNode = $new->apply(
            new AnonymousClassNode()
        );
    }
}
