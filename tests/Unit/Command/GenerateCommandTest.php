<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use webignition\BaseBasilTestCase\AbstractBaseTest as BasilBaseTest;
use webignition\BasilCliCompiler\Command\GenerateCommand;
use webignition\BasilCliCompiler\Services\Compiler;
use webignition\BasilCliCompiler\Services\ErrorOutputFactory;
use webignition\BasilCliCompiler\Services\OutputRenderer;
use webignition\BasilCliCompiler\Services\TestWriter;
use webignition\BasilCliCompiler\Services\ValidatorInvalidResultSerializer;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;
use webignition\BasilCompilerModels\Configuration;
use webignition\BasilCompilerModels\ErrorOutput;
use webignition\BasilCompilerModels\ErrorOutputInterface;
use webignition\BasilLoader\SourceLoader;

class GenerateCommandTest extends AbstractBaseTest
{
    /**
     * @param array<string, string> $input
     * @param int $validationErrorCode
     * @param ErrorOutputInterface $expectedCommandOutput
     *
     * @dataProvider runFailureDataProvider
     */
    public function testRunFailure(
        array $input,
        int $validationErrorCode,
        ErrorOutputInterface $expectedCommandOutput
    ): void {
        $output = new BufferedOutput();

        $command = $this->createCommand(
            \Mockery::mock(Compiler::class),
            \Mockery::mock(TestWriter::class),
            $output
        );

        $exitCode = $command->run(
            new ArrayInput($input),
            $output
        );

        self::assertSame($validationErrorCode, $exitCode);

        $commandOutput = ErrorOutput::fromArray((array) Yaml::parse($output->fetch()));
        self::assertEquals($expectedCommandOutput, $commandOutput);
    }

    public function runFailureDataProvider(): array
    {
        $root = getcwd();

        $emptySourceConfiguration = new Configuration(
            '',
            $root . '/tests/build/target',
            BasilBaseTest::class
        );

        $emptyTargetConfiguration = new Configuration(
            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
            '',
            BasilBaseTest::class
        );

        return [
            'source empty' => [
                'input' => [
                    '--source' => '',
                    '--target' => getcwd() . '/tests/build/target',
                ],
                'validationErrorCode' => ErrorOutputFactory::CODE_COMMAND_CONFIG_SOURCE_EMPTY,
                'expectedCommandOutput' => new ErrorOutput(
                    $emptySourceConfiguration,
                    'source empty; call with --source=SOURCE',
                    ErrorOutputFactory::CODE_COMMAND_CONFIG_SOURCE_EMPTY
                ),
            ],
            'target empty' => [
                'input' => [
                    '--source' => getcwd() . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                    '--target' => '',
                ],
                'validationErrorCode' => ErrorOutputFactory::CODE_COMMAND_CONFIG_TARGET_EMPTY,
                'expectedCommandOutput' => new ErrorOutput(
                    $emptyTargetConfiguration,
                    'target empty; call with --target=TARGET',
                    ErrorOutputFactory::CODE_COMMAND_CONFIG_TARGET_EMPTY
                ),
            ],
        ];
    }

    private function createCommand(
        Compiler $compiler,
        TestWriter $testWriter,
        OutputInterface $commandOutput
    ): GenerateCommand {
        return new GenerateCommand(
            SourceLoader::createLoader(),
            $compiler,
            $testWriter,
            new ErrorOutputFactory(new ValidatorInvalidResultSerializer()),
            new OutputRenderer($commandOutput)
        );
    }
}
