<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\Node;
use Bfg\Comcode\QueryNode;
use Bfg\Comcode\Subjects\ClassSubject;
use Bfg\Comcode\Subjects\EnumSubject;
use Bfg\Comcode\Subjects\SubjectAbstract;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\EnumCase;
use PhpParser\NodeAbstract;

class EnumCaseNode extends QueryNode implements
    ClarificationNodeInterface, ReconstructionNodeInterface, BirthNodeInterface
{
    /**
     * @var ClassConst|null
     */
    public ?NodeAbstract $node = null;

    /**
     * @var SubjectAbstract|EnumSubject
     */
    public SubjectAbstract|EnumSubject $subject;

    /**
     * @param  string  $name
     * @param  mixed|null  $value
     */
    public function __construct(
        public string $name,
        public mixed $value = null,
    ) {
        $this->name = strtoupper($this->name);
    }

    /**
     * Has modifies
     * @return bool
     */
    public static function modified(): bool
    {
        return true;
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public function nodeClass(): string
    {
        return EnumCase::class;
    }

    /**
     * @param  EnumCase|mixed  $stmt
     * @param  string|int  $key
     * @return bool
     */
    public function clarification(mixed $stmt, string|int $key): bool
    {
        return ((string) $stmt->name) == $this->name;
    }

    /**
     * NODE birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        return Node::enumCase(
            $this->name,
            $this->value
        );
    }

    /**
     * STMT reconstruction method
     * @return void
     */
    public function reconstruction(): void
    {
        $this->node->name = Node::identifier($this->name);
        $this->node->expr = Comcode::defineValueNode($this->value);
    }
}
