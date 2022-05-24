<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\Node;
use Bfg\Comcode\QueryNode;
use Bfg\Comcode\Subjects\ClassSubject;
use Bfg\Comcode\Subjects\SubjectAbstract;
use Bfg\Comcode\Traits\CommonWhileExpressions;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeAbstract;

class ClassMethodNode extends QueryNode implements
    ClarificationNodeInterface, ReconstructionNodeInterface, BirthNodeInterface
{
    use CommonWhileExpressions;

    /**
     * @var ClassMethod|null
     */
    public ?NodeAbstract $node = null;

    /**
     * @var QueryNode|NamespaceNode
     */
    public QueryNode|NamespaceNode $parent;

    /**
     * @var SubjectAbstract|ClassSubject
     */
    public SubjectAbstract|ClassSubject $subject;

    /**
     * @param  string  $modifier
     * @param  string|array  $name
     */
    public function __construct(
        public ?string $modifier,
        public string|array $name,
    ) {
        $this->name = is_string($this->name) && str_contains($this->name, ':')
            ? explode(':', $this->name) : $this->name;
    }

    /**
     * @param  ClassMethod|mixed  $stmt
     * @return bool
     */
    public function clarification(mixed $stmt): bool
    {
        if (
            (string) $stmt->name
            == (is_array($this->name) ? $this->name[1] : $this->name)
        ) {
            return true;
        }
        return false;
    }

    /**
     * NODE birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        return Node::method($this->modifier, $this->name);
    }

    /**
     * STMT reconstruction method
     * @return void
     */
    public function reconstruction(): void
    {
        $name = (is_array($this->name) ? $this->name[1] : $this->name);
        $type = (is_array($this->name) ? $this->name[0] : null);
        $this->node->name = Node::identifier($name);
        $this->node->returnType = $type ? Node::name($type) : null;
        $this->node->flags = Comcode::detectPropertyModifier(
            $this->modifier, $this->node->flags
        );
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public static function nodeClass(): string
    {
        return ClassMethod::class;
    }

    /**
     * Has modifies
     * @return bool
     */
    public static function modified(): bool
    {
        return true;
    }
}