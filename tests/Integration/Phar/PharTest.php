<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Integration\Phar;

use Symfony\Component\Process\Process;
use webignition\BasilCliCompiler\Model\ErrorOutput;
use webignition\BasilCliCompiler\Model\OutputInterface;
use webignition\BasilCliCompiler\Model\SuccessOutput;
use webignition\BasilCliCompiler\PharCompiler;
use webignition\BasilCliCompiler\Services\ProjectRootPathProvider;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\CircularStepImportDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\EmptyTestDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\InvalidPageDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\InvalidTestDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\NonLoadableDataDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\NonRetrievableImportDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\ParseExceptionDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\UnknownElementDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\UnknownItemDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\UnknownPageElementDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunFailure\UnknownTestDataProviderTrait;
use webignition\BasilCliCompiler\Tests\DataProvider\RunSuccess\SuccessDataProviderTrait;

class PharTest extends \PHPUnit\Framework\TestCase
{
    use NonLoadableDataDataProviderTrait;
    use CircularStepImportDataProviderTrait;
    use EmptyTestDataProviderTrait;
    use InvalidPageDataProviderTrait;
    use InvalidTestDataProviderTrait;
    use NonRetrievableImportDataProviderTrait;
    use ParseExceptionDataProviderTrait;
    use UnknownElementDataProviderTrait;
    use UnknownItemDataProviderTrait;
    use UnknownPageElementDataProviderTrait;
    use UnknownTestDataProviderTrait;
    use SuccessDataProviderTrait;

    private string $expectedPharPath = '';

    protected function setUp(): void
    {
        parent::setUp();

        $projectRootPath = (new ProjectRootPathProvider())->get();
        $this->expectedPharPath = $projectRootPath . '/' . PharCompiler::DEFAULT_PHAR_FILENAME;

        $this->assertFileExists($this->expectedPharPath);
    }

    /**
     * @param array<mixed> $input
     * @param int $expectedExitCode
     *
     * @dataProvider nonLoadableDataDataProvider
     * @dataProvider circularStepImportDataProvider
     * @dataProvider emptyTestDataProvider
     * @dataProvider invalidPageDataProvider
     * @dataProvider invalidTestDataProvider
     * @dataProvider nonRetrievableImportDataProvider
     * @dataProvider parseExceptionDataProvider
     * @dataProvider unknownElementDataProvider
     * @dataProvider unknownItemDataProvider
     * @dataProvider unknownPageElementDataProvider
     * @dataProvider unknownTestDataProvider
     * @dataProvider successDataProvider
     */
    public function testRun(array $input, int $expectedExitCode, OutputInterface $expectedCommandOutput)
    {
        $modifiedInput = [];

        array_walk($input, function ($value, $key) use (&$modifiedInput) {
            $modifiedInput[] = $key . '=' . $value;
        });

        $pharProcess = new Process(array_merge(
            [
                'php',
                $this->expectedPharPath,
            ],
            $modifiedInput
        ));

        $exitCode = $pharProcess->run();

        $this->assertSame($expectedExitCode, $exitCode);

        $commandOutput = 0 === $exitCode
            ? SuccessOutput::fromJson($pharProcess->getOutput())
            : ErrorOutput::fromJson($pharProcess->getOutput());

        $this->assertEquals($expectedCommandOutput, $commandOutput);

        if ($commandOutput instanceof SuccessOutput) {
            $generatedTestsToRemove = array_unique($commandOutput->getTestPaths());

            foreach ($generatedTestsToRemove as $path) {
                self::assertFileExists($path);
                self::assertFileIsReadable($path);

                unlink($path);
            }
        }
    }
}