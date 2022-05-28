<?php

namespace Bfg\Comcode;

use ArrayIterator;
use Bfg\Comcode\Subjects\FileSubject;
use ErrorException;
use PhpCsFixer\Config;
use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Runner\Runner;
use PhpCsFixer\ToolInfo;
use PhpCsFixer\ToolInfoInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Traversable;

class CSFixer
{
    /**
     * @var bool
     */
    protected static bool $init = false;

    /**
     * @var EventDispatcherInterface|EventDispatcher
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @var ErrorsManager
     */
    private ErrorsManager $errorsManager;

    /**
     * @var ConfigInterface|Config
     */
    private ConfigInterface $defaultConfig;

    /**
     * @var ToolInfoInterface|ToolInfo
     */
    private ToolInfoInterface $toolInfo;

    /**
     * @var Runner
     */
    private Runner $runner;

    /**
     * @throws ErrorException
     */
    public function __construct(
        protected FileSubject $file
    ) {
        $this->eventDispatcher = new EventDispatcher();
        $this->errorsManager = new ErrorsManager();
        $this->defaultConfig = new Config();
        $this->toolInfo = new ToolInfo();
        $this->environment();
    }

    /**
     * Set environment for fixer
     * @return void
     */
    protected function environment(): void
    {
        $resolver = new ConfigurationResolver(
            $this->defaultConfig,
            [
                'config' => __DIR__.'/../.php-cs-fixer.php',
                'path' => [$this->file->file],
                'path-mode' => ConfigurationResolver::PATH_MODE_OVERRIDE,
                'verbosity' => false,
                'show-progress' => false,
                'dry-run' => false,
                'stop-on-violation' => false,
                'allow-risky' => 'yes',
                'cache-file' => false,
                'diff' => true,
                'format' => 1,
                'rules' => null,
                'using-cache' => 'no',
            ],
            getcwd(),
            $this->toolInfo
        );

        $progressType = $resolver->getProgress();

        /** @var Traversable $finder */
        $finder = $resolver->getFinder();

        $this->runner = new Runner(
            new ArrayIterator(iterator_to_array($finder)),
            $resolver->getFixers(),
            $resolver->getDiffer(),
            'none' !== $progressType ? $this->eventDispatcher : null,
            $this->errorsManager,
            $resolver->getLinter(),
            $resolver->isDryRun(),
            $resolver->getCacheManager(),
            $resolver->getDirectory(),
            $resolver->shouldStopOnViolation()
        );
    }

    /**
     * @param  FileSubject  $file
     * @return static
     * @throws ErrorException
     */
    public static function new(
        FileSubject $file
    ): static {
        return new static($file);
    }

    /**
     * Run fixer process
     * @return string|null
     */
    public function run(): ?string
    {
        $result = $this->runner->fix();

        $file = array_key_first($result);

        return $file ? $result[$file]['diff'] : null;
    }
}
