<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\Node;
use Bfg\Comcode\QueryNode;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\NodeAbstract;

class ClassPropertyNode extends QueryNode implements
    ClarificationNodeInterface, ReconstructionNodeInterface, BirthNodeInterface
{
    /**
     * @var Property|null
     */
    public ?NodeAbstract $node = null;

    /**
     * @var ClassNode|null
     */
    public ?QueryNode $parent;

    /**
     * @param  string|null  $modifier
     * @param  string|array  $name
     * @param  mixed|null  $default
     */
    public function __construct(
        public ?string $modifier,
        public string|array $name,
        public mixed $default = null,
    ) {
        $this->name = is_string($this->name) && str_contains($this->name, ':')
            ? explode(':', $this->name) : $this->name;
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public function nodeClass(): string
    {
        return Property::class;
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
     * @param  Property|PropertyProperty[]|mixed  $stmt
     * @return bool
     */
    public function clarification(mixed $stmt): bool
    {
        return ((string) $stmt->props[0]->name
                == (is_array($this->name) ? $this->name[1] : $this->name));
    }

    /**
     * NODE birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        return Node::property($this->modifier, $this->name, $this->default);
    }

    /**
     * STMT reconstruction method
     * @return void
     */
    public function reconstruction(): void
    {
        $name = (is_array($this->name) ? $this->name[1] : $this->name);
        $type = (is_array($this->name) ? $this->name[0] : null);
        $this->node->props[0]->name = Node::varId($name);
        $this->node->props[0]->default = !is_null($this->default)
            ? Comcode::defineValueNode($this->default)
            : null;
        $this->node->type = $type ? Node::name($type) : null;
        $this->node->flags = Comcode::detectPropertyModifier(
            $this->modifier, $this->node->flags
        );
    }
}
