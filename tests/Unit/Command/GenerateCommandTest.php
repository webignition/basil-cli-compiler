<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;
use webignition\BaseBasilTestCase\AbstractBaseTest as BasilBaseTest;
use webignition\BasilCliCompiler\Command\GenerateCommand;
use webignition\BasilCliCompiler\Services\Compiler;
use webignition\BasilCliCompiler\Services\ErrorOutputFactory;
use webignition\BasilCliCompiler\Services\OutputRenderer;
use webignition\BasilCliCompiler\Services\TestWriter;
use webignition\BasilCliCompiler\Services\ValidatorInvalidResultSerializer;
use webignition\BasilCliCompiler\Tests\Services\ProjectRootPathProvider;
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
        $command = $this->createCommand(
            \Mockery::mock(Compiler::class),
            \Mockery::mock(TestWriter::class)
        );

        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute($input);
        self::assertSame($validationErrorCode, $exitCode);

        $output = $commandTester->getDisplay();
        $commandOutput = ErrorOutput::fromArray((array) Yaml::parse($output));
        self::assertEquals($expectedCommandOutput, $commandOutput);
    }

    public function runFailureDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();

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
                'validationErrorCode' => ErrorOutput::CODE_COMMAND_CONFIG_SOURCE_EMPTY,
                'expectedCommandOutput' => new ErrorOutput(
                    $emptySourceConfiguration,
                    'source empty; call with --source=SOURCE',
                    ErrorOutput::CODE_COMMAND_CONFIG_SOURCE_EMPTY
                ),
            ],
            'target empty' => [
                'input' => [
                    '--source' => getcwd() . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                    '--target' => '',
                ],
                'validationErrorCode' => ErrorOutput::CODE_COMMAND_CONFIG_TARGET_EMPTY,
                'expectedCommandOutput' => new ErrorOutput(
                    $emptyTargetConfiguration,
                    'target empty; call with --target=TARGET',
                    ErrorOutput::CODE_COMMAND_CONFIG_TARGET_EMPTY
                ),
            ],
        ];
    }

    private function createCommand(
        Compiler $compiler,
        TestWriter $testWriter
    ): GenerateCommand {
        return new GenerateCommand(
            SourceLoader::createLoader(),
            $compiler,
            $testWriter,
            new ErrorOutputFactory(new ValidatorInvalidResultSerializer()),
            new OutputRenderer(),
            (new ProjectRootPathProvider())->get()
        );
    }
}
