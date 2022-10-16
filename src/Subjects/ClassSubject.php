<?php

namespace Bfg\Comcode\Subjects;

use Bfg\Comcode\Exceptions\QueryNodeError;
use Bfg\Comcode\Node;
use Bfg\Comcode\Nodes\ClassConstNode;
use Bfg\Comcode\Nodes\ClassExtendsNode;
use Bfg\Comcode\Nodes\ClassImplementNode;
use Bfg\Comcode\Nodes\ClassMethodNode;
use Bfg\Comcode\Nodes\ClassNode;
use Bfg\Comcode\Nodes\ClassPropertyNode;
use Bfg\Comcode\Nodes\ClassTraitNode;
use Bfg\Comcode\Nodes\NamespaceNode;
use Bfg\Comcode\Nodes\NamespaceUseNode;
use Bfg\Comcode\QueryNode;

/**
 * @method ClassExtendsNode extends (string $namespace)
 * @method bool forgetExtends (string $namespace)
 * @method bool existsExtends (string $namespace)
 * @method bool notExistsExtends (string $namespace)
 * @method ClassImplementNode implement(string $namespace)
 * @method bool forgetImplement(string $namespace)
 * @method bool existsImplement(string $namespace)
 * @method bool notExistsImplement(string $namespace)
 * @method ClassConstNode const(string $modifier, string $name, mixed $value = null)
 * @method ClassConstNode publicConst(string $name, mixed $value = null)
 * @method ClassConstNode protectedConst(string $name, mixed $value = null)
 * @method ClassConstNode privateConst(string $name, mixed $value = null)
 * @method bool forgetConst(string $name, mixed $value = null)
 * @method bool existsConst(string $name, mixed $value = null)
 * @method bool notExistsConst(string $name, mixed $value = null)
 * @method ClassPropertyNode property(string $modifier, string|array $name, mixed $default = null)
 * @method ClassPropertyNode publicProperty(string|array $name, mixed $default = null)
 * @method ClassPropertyNode publicStaticProperty(string|array $name, mixed $default = null)
 * @method ClassPropertyNode protectedProperty(string|array $name, mixed $default = null)
 * @method ClassPropertyNode protectedStaticProperty(string|array $name, mixed $default = null)
 * @method ClassPropertyNode privateProperty(string|array $name, mixed $default = null)
 * @method bool forgetProperty(string|array $name, mixed $default = null)
 * @method bool existsProperty(string|array $name, mixed $default = null)
 * @method bool notExistsProperty(string|array $name, mixed $default = null)
 * @method ClassMethodNode method(string $modifier, string|array $name)
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
 * @method bool forgetMethod(string|array $name)
 * @method bool existsMethod(string|array $name)
 * @method bool notExistsMethod(string|array $name)
 */
class ClassSubject extends SubjectAbstract
{
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

    static array $events = [];

    /**
     * Cached data
     * @var array
     */
    protected array $cache = [];

    /**
     * @var ClassNode
     */
    public ClassNode $classNode;

    /**
     * @var NamespaceNode
     */
    public NamespaceNode $namespaceNode;

    /**
     * @param  FileSubject  $fileSubject
     * @param  object|string  $class
     */
    public function __construct(
        public FileSubject $fileSubject,
        public object|string $class,
    ) {
        parent::__construct($this->fileSubject);
    }

    /**
     * @param  string  $nodeName
     * @param  callable  $cb
     * @return void
     */
    public static function on(string $nodeName, callable $cb): void
    {
        static::$events[$nodeName][] = $cb;
    }

    /**
     * @param  string  $namespace
     * @return NamespaceUseNode
     */
    public function use(
        string $namespace
    ): NamespaceUseNode {
        return $this->namespaceNode->apply(
            new NamespaceUseNode($namespace)
        );
    }

    /**
     * @param  string  $namespace
     * @return ClassTraitNode
     */
    public function trait(
        string $namespace
    ): ClassTraitNode {
        return $this->classNode->apply(
            new ClassTraitNode($namespace)
        );
    }

    /**
     * @param  string  $namespace
     * @return bool
     */
    public function forgetTrait(
        string $namespace
    ): bool {
        return $this->classNode->forget(
            new ClassTraitNode($namespace)
        );
    }

    /**
     * @param  string|callable  $text
     * @return $this
     */
    public function comment(
        string|callable $text
    ): static {
        if (is_callable($text)) {
            $comment = $this->node?->getDocComment();
            $doc = new DocSubject($this);
            call_user_func($text, $doc, $comment, $this);
            $text = $doc->render();
        }

        $this->classNode->node?->setDocComment(
            Node::doc($text)
        );

        return $this;
    }

    /**
     * @throws QueryNodeError
     */
    public function __call(string $name, array $arguments)
    {
        if (
            preg_match(
                '/^forget('.implode('|', array_keys(static::$classNodes)).')$/i',
                $name,
                $matches
            )
        ) {
            return $this->classNode->forget(
                $this->detectInsideNodeByMathes($matches, $arguments)
            );
        } else {
            if (
                preg_match(
                    '/^exists('.implode('|', array_keys(static::$classNodes)).')$/i',
                    $name,
                    $matches
                )
            ) {
                return $this->classNode->exists(
                    $this->detectInsideNodeByMathes($matches, $arguments)
                );
            } else {
                if (
                    preg_match(
                        '/^notExists('.implode('|', array_keys(static::$classNodes)).')$/i',
                        $name,
                        $matches
                    )
                ) {
                    return !$this->classNode->exists(
                        $this->detectInsideNodeByMathes($matches, $arguments)
                    );
                } else {
                    if (
                        preg_match(
                            '/^([a-zA-Z]+)?('.implode('|', array_keys(static::$classNodes)).')$/i',
                            $name,
                            $matches
                        )
                    ) {
                        return $this->classNode->apply(
                            $this->detectInsideNodeByMathesWithModifier($matches, $arguments)
                        );
                    }
                }
            }
        }

        throw new QueryNodeError("Node controller [$name] not fond!");
    }

    protected function detectInsideNodeByMathes(
        array $matches,
        array $arguments,
    ): QueryNode {
        $nodeName = strtolower($matches[1]);
        /** @var QueryNode $node */
        $node = static::$classNodes[$nodeName]::modified()
            ? new static::$classNodes[$nodeName](null, ...$arguments)
            : new static::$classNodes[$nodeName](...$arguments);
        $node->subject = $this;
        if (isset(static::$events[$nodeName]) && static::$events[$nodeName]) {
            foreach (static::$events[$nodeName] as $event) {
                if (is_callable($event)) {
                    call_user_func($event, $this, $node, ...$arguments);
                }
            }
        }
        return $node;
    }

    protected function detectInsideNodeByMathesWithModifier(
        array $matches,
        array $arguments,
    ): QueryNode {
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
        $node->subject = $this;
        if (isset(static::$events[$nodeName]) && static::$events[$nodeName]) {
            foreach (static::$events[$nodeName] as $event) {
                if (is_callable($event)) {
                    call_user_func($event, $this, $node, ...$arguments);
                }
            }
        }
        return $node;
    }

    /**
     * Get content of class file
     * @return string
     */
    public function content(): string
    {
        return $this->fileSubject->content();
    }

    /**
     * Delete class file
     * @return bool
     */
    public function delete(): bool
    {
        return $this->fileSubject->delete();
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

    /**
     * @param  string  $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        return $this->cache[$name] ?? null;
    }

    /**
     * @param  string  $name
     * @param $value
     * @return void
     */
    public function __set(string $name, $value): void
    {
        $this->cache[$name] = $value;
    }
}
