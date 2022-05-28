<?php

namespace Bfg\Comcode\Subjects;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\Query;
use Bfg\Comcode\QueryNode;
use Bfg\Comcode\Traits\Conditionable;
use JetBrains\PhpStorm\NoReturn;
use PhpParser\NodeAbstract;
use Stringable;

abstract class SubjectAbstract implements Stringable
{
    use Conditionable;

    /**
     * @var array
     */
    public array $nodes = [];

    /**
     * @var NodeAbstract|\Bfg\Comcode\AnonymousStmt
     */
    public NodeAbstract $node;

    /**
     * @param  FileSubject  $fileSubject
     */
    public function __construct(
        public FileSubject $fileSubject
    ) {
        $this->nodes = Comcode::parsPhpFile($this->fileSubject->file);
        $this->node = Comcode::anonymousStmt($this->nodes);

        $this->discoverStmtEnvironment();
    }

    /**
     * Discover individual node environment
     * @return void
     */
    abstract protected function discoverStmtEnvironment(): void;

    /**
     * @param  mixed  ...$params
     * @return $this
     */
    public static function create(...$params): static
    {
        return new static(...$params);
    }

    /**
     * Create new query node content
     * @template QUERY_NODE
     * @param  QUERY_NODE|QueryNode  $nodeClass
     * @return QUERY_NODE
     */
    public function apply(
        QueryNode $nodeClass
    ): QueryNode {
        $nodeClass->subject = $this;

        $query = Query::new($this->nodes)->isA(
            $nodeClass::nodeClass()
        )->filter(
            $nodeClass instanceof ClarificationNodeInterface
                ? [$nodeClass, 'clarification'] : null
        );

        $key = $query->firstKey();

        $nodeClass->node = $query->first();

        $nodeClass->isMatch()
            ? $nodeClass instanceof ReconstructionNodeInterface && $nodeClass->reconstruction()
            : $nodeClass instanceof BirthNodeInterface && $nodeClass->node = $nodeClass->birth();

        if (is_int($key)) {
            $this->nodes[$key] = $nodeClass->node;
        } else {
            $this->nodes = [$nodeClass->node];
        }

        return $nodeClass;
    }

    /**
     * Save nodes to file
     * @return string|null
     */
    public function save(): ?string
    {
        return $this->fileSubject->update($this)->fix();
    }

    /**
     * @return string
     */
    public function content(): string
    {
        return $this->fileSubject->content();
    }

    /**
     * @param  bool  $self
     * @return void
     */
    #[NoReturn] public function dd(
        bool $self = false
    ): void {
        dd($self ? $this->node->nodes : $this->__toString());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return Comcode::printStmt(
            $this->toStmt(),
            true
        );
    }

    /**
     * Create node list from collection items
     * @return array
     */
    public function toStmt(): array
    {
        return Comcode::undressNodes($this->nodes);
    }
}
