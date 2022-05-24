<?php

namespace Bfg\Comcode\Subjects;

use Bfg\Comcode\Exceptions\QueryNodeError;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Nodes\ClassConstNode;
use Bfg\Comcode\Nodes\ClassExtendsNode;
use Bfg\Comcode\Nodes\ClassImplementNode;
use Bfg\Comcode\Nodes\ClassMethodNode;
use Bfg\Comcode\Nodes\ClassNode;
use Bfg\Comcode\Nodes\ClassPropertyNode;
use Bfg\Comcode\Nodes\NamespaceNode;
use Bfg\Comcode\Nodes\NamespaceUseNode;
use Bfg\Comcode\Query;
use ErrorException;

/**
 * @method ClassExtendsNode extends(string $namespace)
 * @method ClassImplementNode implement(string $namespace)
 * @method ClassConstNode forgetConst(string $name, mixed $value = null)
 * @method ClassConstNode publicConst(string $name, mixed $value = null)
 * @method ClassConstNode protectedConst(string $name, mixed $value = null)
 * @method ClassConstNode privateConst(string $name, mixed $value = null)
 * @method ClassPropertyNode forgetProperty(string|array $name, mixed $default = null)
 * @method ClassPropertyNode publicProperty(string|array $name, mixed $default = null)
 * @method ClassPropertyNode publicStaticProperty(string|array $name, mixed $default = null)
 * @method ClassPropertyNode protectedProperty(string|array $name, mixed $default = null)
 * @method ClassPropertyNode protectedStaticProperty(string|array $name, mixed $default = null)
 * @method ClassPropertyNode privateProperty(string|array $name, mixed $default = null)
 * @method ClassMethodNode forgetMethod(string|array $name)
 * @method ClassMethodNode publicMethod(string|array $name)
 * @method ClassMethodNode publicStaticMethod(string|array $name)
 * @method ClassMethodNode protectedMethod(string|array $name)
 * @method ClassMethodNode protectedStaticMethod(string|array $name)
 * @method ClassMethodNode privateMethod(string|array $name)
 * @method ClassMethodNode finalPublicMethod(string|array $name)
 * @method ClassMethodNode finalPublicStaticMethod(string|array $name)
 * @method ClassMethodNode finalProtectedMethod(string|array $name)
 * @method ClassMethodNode finalProtectedStaticMethod(string|array $name)
 * @method ClassMethodNode finalPrivateMethod(string|array $name)
 * @method ClassMethodNode abstractPublicMethod(string|array $name)
 * @method ClassMethodNode abstractPublicStaticMethod(string|array $name)
 * @method ClassMethodNode abstractProtectedMethod(string|array $name)
 * @method ClassMethodNode abstractProtectedStaticMethod(string|array $name)
 * @method ClassMethodNode abstractPrivateMethod(string|array $name)
 * @method ClassMethodNode abstractFinalPublicMethod(string|array $name)
 * @method ClassMethodNode abstractFinalPublicStaticMethod(string|array $name)
 * @method ClassMethodNode abstractFinalProtectedMethod(string|array $name)
 * @method ClassMethodNode abstractFinalProtectedStaticMethod(string|array $name)
 * @method ClassMethodNode abstractFinalPrivateMethod(string|array $name)
 */
class ClassSubject extends SubjectAbstract
{
    /**
     * @var ClassNode
     */
    protected ClassNode $classNode;

    /**
     * @var NamespaceNode
     */
    protected NamespaceNode $namespaceNode;

    /**
     * @var array|<class-string>[]
     */
    protected static array $classNodes = [
        'extends' => ClassExtendsNode::class,
        'implement' => ClassImplementNode::class,
        'const' => ClassConstNode::class,
        'property' => ClassPropertyNode::class,
        'method' => ClassMethodNode::class,
    ];

    /**
     * @param  FileSubject  $fileSubject
     * @param  object|string  $class
     * @throws ErrorException
     */
    public function __construct(
        public FileSubject $fileSubject,
        public object|string $class,
    ) {
        parent::__construct($this->fileSubject);
    }

    /**
     * @param  string  $namespace
     * @return NamespaceUseNode
     */
    public function use(string $namespace): NamespaceUseNode
    {
        return $this->namespaceNode->apply(
            new NamespaceUseNode($namespace)
        );
    }

    /**
     * @throws QueryNodeError
     */
    public function __call(string $name, array $arguments)
    {
        if (
            preg_match(
                '/^forget('. implode('|', array_keys(static::$classNodes)) .')$/i',
                $name,
                $matches
            )
        ) {
            $nodeName = strtolower($matches[1]);
            $node = static::$classNodes[$nodeName]::modified()
                ? new static::$classNodes[$nodeName](null, ...$arguments)
                : new static::$classNodes[$nodeName](...$arguments);
            $store = $node->store;
            $query = Query::new((array) $this->classNode->node->{$store})
                ->isA($node::nodeClass())
                ->filter(
                    $node instanceof ClarificationNodeInterface
                        ? [$node, 'clarification'] : null
                );

            $key = $query->firstKey();

            if (property_exists($this->classNode->node, $store)) {
                if (is_array($this->classNode->node->{$store})) {
                    if (is_int($key)) {
                        $arr = $this->classNode->node->{$store};
                        unset($arr[$key]);
                        $this->classNode->node->{$store} = array_values($arr);
                        return true;
                    }
                } else {
                    $this->classNode->node->{$store} = null;
                    return true;
                }
            }
            return false;
        }

        if (
            preg_match(
                '/^([a-zA-Z]+)?('. implode('|', array_keys(static::$classNodes)) .')$/i',
                $name,
                $matches
            )
        ) {
            $modifier = $matches[1] ?? null;
            $modifier = $modifier
                ? str_replace(
                    '_',
                    ' ',
                    strtolower(preg_replace('/([A-Z])/', '_$1', $modifier))
                ) : null;
            $nodeName = strtolower($matches[2]);
            $node = $modifier
                ? new static::$classNodes[$nodeName]($modifier, ...$arguments)
                : new static::$classNodes[$nodeName](...$arguments);
            return $this->classNode->apply($node);
        }

        throw new QueryNodeError("Node controller [$name] not fond!");
    }

    /**
     * @param  callable  $callback
     * @return $this
     */
    public function body(callable $callback): static
    {
        call_user_func($callback, $this->classNode, $this);

        return $this;
    }

    /**
     * Discover individual environment
     * @return void
     */
    protected function discoverStmtEnvironment(): void
    {
        $this->namespaceNode = $this->apply(
            new NamespaceNode($this->class)
        );

        $this->classNode = $this->namespaceNode->apply(
            new ClassNode($this->class)
        );
    }
}
