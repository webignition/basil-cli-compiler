<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as ConsoleOutputInterface;
use Symfony\Component\Yaml\Yaml;
use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Exception\UnresolvedPlaceholderException;
use webignition\BasilCliCompiler\Model\ErrorOutputInterface;
use webignition\BasilCliCompiler\Model\OutputInterface;
use webignition\BasilCliCompiler\Model\SuiteManifest;
use webignition\BasilCliCompiler\Services\Compiler;
use webignition\BasilCliCompiler\Services\ConfigurationFactory;
use webignition\BasilCliCompiler\Services\ConfigurationValidator;
use webignition\BasilCliCompiler\Services\ErrorOutputFactory;
use webignition\BasilCliCompiler\Services\TestWriter;
use webignition\BasilCompilableSourceFactory\Exception\UnsupportedStepException;
use webignition\BasilLoader\Exception\EmptyTestException;
use webignition\BasilLoader\Exception\InvalidPageException;
use webignition\BasilLoader\Exception\InvalidTestException;
use webignition\BasilLoader\Exception\NonRetrievableImportException;
use webignition\BasilLoader\Exception\ParseException;
use webignition\BasilLoader\Exception\UnknownTestException;
use webignition\BasilLoader\Exception\YamlLoaderException;
use webignition\BasilLoader\SourceLoader;
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

    private SourceLoader $sourceLoader;
    private Compiler $compiler;
    private TestWriter $testWriter;
    private ConfigurationFactory $configurationFactory;
    private ConfigurationValidator $configurationValidator;
    private ErrorOutputFactory $errorOutputFactory;
    private string $projectRootPath;

    public function __construct(
        SourceLoader $sourceLoader,
        Compiler $compiler,
        TestWriter $testWriter,
        ConfigurationFactory $configurationFactory,
        ConfigurationValidator $configurationValidator,
        ErrorOutputFactory $errorOutputFactory,
        string $projectRootPath
    ) {
        parent::__construct();

        $this->sourceLoader = $sourceLoader;
        $this->compiler = $compiler;
        $this->testWriter = $testWriter;
        $this->configurationFactory = $configurationFactory;
        $this->configurationValidator = $configurationValidator;
        $this->errorOutputFactory = $errorOutputFactory;
        $this->projectRootPath = $projectRootPath;
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

        $configuration = $this->configurationFactory->create($rawSource, $rawTarget, $baseClass);

        $commandOutput = null;

        if ('' === $rawSource) {
            $commandOutput = $this->errorOutputFactory->createForEmptySource($configuration);
        }

        if (null === $commandOutput && '' === $rawTarget) {
            $commandOutput = $this->errorOutputFactory->createForEmptyTarget($configuration);
        }

        if (null === $commandOutput && false === $this->configurationValidator->isValid($configuration)) {
            $commandOutput = $this->errorOutputFactory->createFromInvalidConfiguration($configuration);
        }

        if ($commandOutput instanceof ErrorOutputInterface) {
            $this->render($output, $commandOutput);

            return $commandOutput->getCode();
        }

        $sourcePaths = $this->createSourcePaths($configuration->getSource());

        $generatedFiles = [];
        foreach ($sourcePaths as $sourcePath) {
            try {
                $testSuite = $this->sourceLoader->load($sourcePath);
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
                UnknownTestException |
                YamlLoaderException $exception
            ) {
                $commandOutput = $this->errorOutputFactory->createForException($exception, $configuration);
                $this->render($output, $commandOutput);

                return $commandOutput->getCode();
            }

            try {
                foreach ($testSuite->getTests() as $test) {
                    $test = $this->removeProjectRootPathFromTestPath($test);
                    $compiledTest = $this->compiler->compile($test, $configuration->getBaseClass());

                    $generatedFiles[] = $this->testWriter->write($compiledTest, $configuration->getTarget());
                }
            } catch (
                UnresolvedPlaceholderException |
                UnsupportedStepException $exception
            ) {
                $commandOutput = $this->errorOutputFactory->createForException($exception, $configuration);
                $this->render($output, $commandOutput);

                return $commandOutput->getCode();
            }
        }

        $this->render($output, new SuiteManifest($configuration, $generatedFiles));

        return 0;
    }

    /**
     * @param string $source
     *
     * @return string[]
     */
    private function createSourcePaths(string $source): array
    {
        $sourcePaths = [];

        if (is_file($source)) {
            $sourcePaths[] = $source;
        }

        if (is_dir($source)) {
            return $this->findSourcePaths($source);
        }

        return $sourcePaths;
    }

    /**
     * @param string $directorySource
     *
     * @return string[]
     */
    private function findSourcePaths(string $directorySource): array
    {
        $sourcePaths = [];

        $directoryIterator = new \DirectoryIterator($directorySource);
        foreach ($directoryIterator as $item) {
            /** @var \DirectoryIterator $item */
            if ($item->isFile() && 'yml' === $item->getExtension()) {
                $sourcePaths[] = $item->getPath() . DIRECTORY_SEPARATOR . $item->getFilename();
            }
        }

        sort($sourcePaths);

        return $sourcePaths;
    }

    private function render(ConsoleOutputInterface $consoleOutput, OutputInterface $commandOutput): int
    {
        $consoleOutput->writeln(Yaml::dump(
            $commandOutput->getData(),
            4
        ));

        return $commandOutput instanceof ErrorOutputInterface ? $commandOutput->getCode() : 0;
    }

    private function removeProjectRootPathFromTestPath(TestInterface $test): TestInterface
    {
        $path = (string) $test->getPath();
        $path = (string) preg_replace('/^' . preg_quote($this->projectRootPath, '/') . '/', '', $path);
        $path = ltrim($path, '/');

        return $test->withPath($path);
    }
}
