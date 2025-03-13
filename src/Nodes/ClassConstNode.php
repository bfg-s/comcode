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
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\NodeAbstract;

class ClassConstNode extends QueryNode implements
    ClarificationNodeInterface, ReconstructionNodeInterface, BirthNodeInterface
{
    /**
     * @var ClassConst|null
     */
    public ?NodeAbstract $node = null;

    /**
     * @var SubjectAbstract|ClassSubject
     */
    public SubjectAbstract|ClassSubject $subject;

    /**
     * @param  string|null  $modifier
     * @param  string  $name
     * @param  mixed|null  $value
     */
    public function __construct(
        public ?string $modifier,
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
        return ClassConst::class;
    }

    /**
     * @param  ClassConst|mixed  $stmt
     * @param  string|int  $key
     * @return bool
     */
    public function clarification(mixed $stmt, string|int $key): bool
    {
        return property_exists($stmt, 'consts') && (string) $stmt->consts[0]->name == $this->name;
    }

    /**
     * NODE birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        return Node::const(
            $this->modifier,
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
        $this->node->consts[0]->name = Node::identifier($this->name);
        $this->node->consts[0]->value = Comcode::defineValueNode($this->value);
        $this->node->flags = $this->modifier ? Comcode::detectPropertyModifier(
            $this->modifier, $this->node->flags
        ) : 0;
    }
}
