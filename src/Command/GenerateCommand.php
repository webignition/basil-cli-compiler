<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as ConsoleOutputInterface;
use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Exception\UnresolvedPlaceholderException;
use webignition\BasilCliCompiler\Services\Compiler;
use webignition\BasilCliCompiler\Services\ErrorOutputFactory;
use webignition\BasilCliCompiler\Services\OutputRenderer;
use webignition\BasilCliCompiler\Services\TestWriter;
use webignition\BasilCompilableSourceFactory\Exception\UnsupportedStepException;
use webignition\BasilCompilerModels\Configuration;
use webignition\BasilCompilerModels\SuiteManifest;
use webignition\BasilCompilerModels\TestManifest;
use webignition\BasilLoader\Exception\EmptyTestException;
use webignition\BasilLoader\Exception\InvalidPageException;
use webignition\BasilLoader\Exception\InvalidTestException;
use webignition\BasilLoader\Exception\NonRetrievableImportException;
use webignition\BasilLoader\Exception\ParseException;
use webignition\BasilLoader\Exception\YamlLoaderException;
use webignition\BasilLoader\TestLoader;
use webignition\BasilModelProvider\Exception\UnknownItemException;
use webignition\BasilModels\Test\TestInterface;
use webignition\BasilResolver\CircularStepImportException;
use webignition\BasilResolver\UnknownElementException;
use webignition\BasilResolver\UnknownPageElementException;
use webignition\SymfonyConsole\TypedInput\TypedInput;

class GenerateCommand extends Command
{
    public const OPTION_SOURCE = 'source';
    public const OPTION_TARGET = 'target';
    public const OPTION_BASE_CLASS = 'base-class';

    private const NAME = 'generate';

    private TestLoader $testLoader;
    private Compiler $compiler;
    private TestWriter $testWriter;
    private ErrorOutputFactory $errorOutputFactory;
    private OutputRenderer $outputRenderer;

    public function __construct(
        TestLoader $testLoader,
        Compiler $compiler,
        TestWriter $testWriter,
        ErrorOutputFactory $errorOutputFactory,
        OutputRenderer $outputRenderer
    ) {
        parent::__construct();

        $this->testLoader = $testLoader;
        $this->compiler = $compiler;
        $this->testWriter = $testWriter;
        $this->errorOutputFactory = $errorOutputFactory;
        $this->outputRenderer = $outputRenderer;
    }

    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Generate tests from basil source')
            ->addOption(
                self::OPTION_SOURCE,
                null,
                InputOption::VALUE_REQUIRED,
                'Path to the basil test source from which to generate tests. ' .
                'Can be absolute or relative to this directory.',
                ''
            )
            ->addOption(
                self::OPTION_TARGET,
                null,
                InputOption::VALUE_REQUIRED,
                'Output path for generated tests',
                ''
            )
            ->addOption(
                self::OPTION_BASE_CLASS,
                null,
                InputOption::VALUE_OPTIONAL,
                'Base class to extend',
                AbstractBaseTest::class
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param ConsoleOutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, ConsoleOutputInterface $output)
    {
        $typedInput = new TypedInput($input);

        $rawSource = trim((string) $typedInput->getStringOption(GenerateCommand::OPTION_SOURCE));
        $rawTarget = trim((string) $typedInput->getStringOption(GenerateCommand::OPTION_TARGET));
        $baseClass = trim((string) $typedInput->getStringOption(GenerateCommand::OPTION_BASE_CLASS));

        $configuration = new Configuration($rawSource, $rawTarget, $baseClass);

        if ('' === $rawSource) {
            return $this->outputRenderer->render($this->errorOutputFactory->createForEmptySource($configuration));
        }

        if ('' === $rawTarget) {
            return $this->outputRenderer->render($this->errorOutputFactory->createForEmptyTarget($configuration));
        }

        $configurationValidationState = $configuration->validate();
        if (Configuration::VALIDATION_STATE_VALID !== $configurationValidationState) {
            return $this->outputRenderer->render(
                $this->errorOutputFactory->createFromInvalidConfiguration($configuration, $configurationValidationState)
            );
        }

        $testManifests = [];

        try {
            $tests = $this->testLoader->load($configuration->getSource());
        } catch (
            CircularStepImportException |
            EmptyTestException |
            InvalidPageException |
            InvalidTestException |
            NonRetrievableImportException |
            ParseException |
            UnknownElementException |
            UnknownItemException |
            UnknownPageElementException |
            YamlLoaderException $exception
        ) {
            return $this->outputRenderer->render(
                $this->errorOutputFactory->createForException($exception, $configuration)
            );
        }

        try {
            foreach ($tests as $test) {
                $relativePathTest = $this->removeRootPathFromTestPath($test);
                $relativePathCompiledTest = $this->compiler->compile(
                    $relativePathTest,
                    $configuration->getBaseClass()
                );

                $target = $this->testWriter->write($relativePathCompiledTest, $configuration->getTarget());

                $testManifests[] = new TestManifest(
                    $test->getConfiguration(),
                    $test->getPath() ?? '',
                    $target,
                    count($test->getSteps())
                );
            }
        } catch (
            UnresolvedPlaceholderException |
            UnsupportedStepException $exception
        ) {
            return $this->outputRenderer->render(
                $this->errorOutputFactory->createForException($exception, $configuration)
            );
        }

        $this->outputRenderer->render(new SuiteManifest($configuration, $testManifests));

        return 0;
    }

    private function removeRootPathFromTestPath(TestInterface $test): TestInterface
    {
        $root = (string) getcwd();

        $path = (string) $test->getPath();
        $path = (string) preg_replace('/^' . preg_quote($root, '/') . '/', '', $path);
        $path = ltrim($path, '/');

        return $test->withPath($path);
    }
}
