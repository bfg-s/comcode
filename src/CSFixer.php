<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Subjects\FileSubject;
use Bfg\Comcode\Subjects\SubjectAbstract;
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
    private EventDispatcherInterface $eventDispatcher;

    private ErrorsManager $errorsManager;

    private ConfigInterface $defaultConfig;

    private ToolInfoInterface $toolInfo;

    private Runner $runner;

    protected static bool $init = false;

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
                'config' => base_path('.php-cs-fixer.php'),
                'path' => [$this->file->file],
                'path-mode' => ConfigurationResolver::PATH_MODE_OVERRIDE,
                'verbosity' => false,
                'show-progress' => false,
                'dry-run' => false,
                'stop-on-violation' => false,
                'allow-risky' => 'no',
                'cache-file' =>  false,
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
            new \ArrayIterator(iterator_to_array($finder)),
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
     * Run fixer process
     * @return string|null
     */
    public function run(): ?string
    {
        $result = $this->runner->fix();

        $file = array_key_first($result);

        return $file ? $result[$file]['diff'] : null;
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
}
