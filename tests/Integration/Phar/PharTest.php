<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Integration\Phar;

use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
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
use webignition\BasilCompilerModels\ErrorOutput;
use webignition\BasilCompilerModels\OutputInterface;
use webignition\BasilCompilerModels\SuiteManifest;

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

        $this->expectedPharPath = getcwd() . '/compiler';

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

        $processOutputData = (array) Yaml::parse($pharProcess->getOutput());

        $suiteManifest = 0 === $exitCode
            ? SuiteManifest::fromArray($processOutputData)
            : ErrorOutput::fromArray($processOutputData);

        $this->assertEquals($expectedCommandOutput, $suiteManifest);

        if ($suiteManifest instanceof SuiteManifest) {
            $generatedTestsToRemove = [];

            foreach ($suiteManifest->getTestManifests() as $testManifest) {
                $testPath = $testManifest->getTarget();
                $generatedTestsToRemove[$testPath] = $testPath;
            }

            foreach ($generatedTestsToRemove as $path) {
                self::assertFileExists($path);
                self::assertFileIsReadable($path);

                unlink($path);
            }
        }
    }
}
