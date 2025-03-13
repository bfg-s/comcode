<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\AnonymousStmt;
use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\AnonymousInterface;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Node;
use Bfg\Comcode\QueryNode;
use Bfg\Comcode\Traits\CommonWhileExpressions;
use PhpParser\Comment;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeAbstract;

class RowNode extends QueryNode implements
    BirthNodeInterface, ClarificationNodeInterface, AnonymousInterface
{
    use CommonWhileExpressions;

    /**
     * @var Expression|null
     */
    public ?NodeAbstract $node = null;

    /**
     * @param  string  $name
     */
    public function __construct(
        public string $name
    ) {
        $this->name
            = '// '.$this->name;
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public function nodeClass(): string
    {
        return Expression::class;
    }

    /**
     * NODE birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        $node = Comcode::anonymousStmt();
        $node->setDocComment(
            Node::doc($this->name)
        );
        return $node;
    }

    /**
     * @param  Expression|mixed  $stmt
     * @param  string|int  $key
     * @return bool
     */
    public function clarification(mixed $stmt, string|int $key): bool
    {
        if ($stmt->hasAttribute('comments')) {
            $attributes
                = $stmt->getAttribute('comments');

            foreach ($attributes as $attribute) {
                if (
                    $attribute instanceof Comment
                ) {
                    if (
                        $attribute->getText() == $this->name
                    ) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
