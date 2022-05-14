<?php

namespace Bfg\Comcode\Subjects;

use Illuminate\Support\Traits\Conditionable;
use JetBrains\PhpStorm\ArrayShape;
use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinter\Standard;

abstract class SubjectAbstract implements \Stringable
{
    use Conditionable;

    /**
     * Abstract constructor
     * @param  Stmt[]  $nodes
     */
    public function __construct(
        protected Stmt|array $nodes = [],
    ) {
        $this->discoverStmtEnvironment();
    }

    /**
     * Discover individual stmt environment
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
     * @return array|null
     */
    #[ArrayShape(['nodes' => "string", 'nodesList' => "string"])]
    public function __debugInfo(): ?array
    {
        return [
            'nodes' => '\PhpParser\Node\Stmt('.count($this->nodes).')',
            'nodesList' => $this->nodes
        ];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (new Standard)
            ->{is_array($this->nodes)
                ? 'prettyPrintFile'
                : 'prettyPrintExpr'
            }($this->nodes);
    }
}
