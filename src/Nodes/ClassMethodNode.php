<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\Node;
use Bfg\Comcode\QueryNode;
use Bfg\Comcode\Traits\CommonWhileExpressions;
use Bfg\Comcode\Traits\FuncCommonTrait;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeAbstract;

class ClassMethodNode extends QueryNode implements
    ClarificationNodeInterface, ReconstructionNodeInterface, BirthNodeInterface
{
    use CommonWhileExpressions;
    use FuncCommonTrait;

    /**
     * @var ClassMethod|null
     */
    public ?NodeAbstract $node = null;

    /**
     * @param  string|null  $modifier
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
     * @return array|mixed|string
     */
    public function getName()
    {
        return is_array($this->name) ? $this->name[1] : $this->name;
    }

    /**
     * @return array|mixed|string
     */
    public function getType()
    {
        return is_array($this->name) ? $this->name[0] : null;
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
        return ClassMethod::class;
    }

    /**
     * @return void
     */
    public function mounting(): void
    {
        if (is_array($this->name)) {
            $this->name[0] = Comcode::useIfClass(
                $this->name[0],
                $this->subject
            );
        }
    }

    /**
     * @param  ClassMethod|mixed  $stmt
     * @param  string|int  $key
     * @return bool
     */
    public function clarification(mixed $stmt, string|int $key): bool
    {
        if (
            (string) $stmt->name == $this->getName()
        ) {
            $stmt->stmts = [];
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
        return Node::method(
            $this->modifier,
            $this->name,
            $this->subject
        );
    }

    /**
     * STMT reconstruction method
     * @return void
     */
    public function reconstruction(): void
    {
        $name = $this->getName();
        $type = $this->getType();
        $this->node->name = Node::identifier($name);
        $this->node->returnType = $type ? Node::name($type) : null;
        $this->node->flags = Comcode::detectPropertyModifier(
            $this->modifier, $this->node->flags
        );
    }
}
