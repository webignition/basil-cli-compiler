<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Integration\Bin;

use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use webignition\BasilCliCompiler\Tests\Integration\AbstractGeneratedTestCase;
use webignition\BasilCliCompiler\Tests\Services\ProjectRootPathProvider;
use webignition\BasilCompilerModels\SuiteManifest;

class CompilerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider generateDataProvider
     *
     * @param string $source
     * @param string $target
     * @param array<mixed> $expectedGeneratedTestDataCollection
     */
    public function testGenerate(string $source, string $target, array $expectedGeneratedTestDataCollection)
    {
        $projectRootPath = (new ProjectRootPathProvider())->get();

        $generateProcess = $this->createGenerateCommandProcess($source, $target);
        $exitCode = $generateProcess->run();

        $this->assertSame(0, $exitCode);

        $generateCommandOutput = SuiteManifest::fromArray((array) Yaml::parse($generateProcess->getOutput()));

        $testPaths = $generateCommandOutput->getTestPaths();
        self::assertNotEmpty($testPaths);

        foreach ($testPaths as $index => $testPath) {
            self::assertFileExists($testPath);

            $expectedGeneratedTestData = $expectedGeneratedTestDataCollection[$index];

            $generatedTestContent = (string) file_get_contents($testPath);

            $classNameReplacement = $expectedGeneratedTestData['classNameReplacement'];
            $generatedTestContent = $this->replaceGeneratedTestClassName($generatedTestContent, $classNameReplacement);
            $generatedTestContent = $this->removeProjectRootPathInGeneratedTest($generatedTestContent);

            $expectedTestContentPath = $projectRootPath . '/' . $expectedGeneratedTestData['expectedContentPath'];
            $expectedTestContent = (string) file_get_contents($expectedTestContentPath);

            $this->assertSame($expectedTestContent, $generatedTestContent);
        }

        foreach ($generateCommandOutput->getTestPaths() as $testPath) {
            unlink($testPath);
        }
    }

    public function generateDataProvider(): array
    {
        return [
            'single test' => [
                'source' => './tests/Fixtures/basil-integration/Test/index-page-open.yml',
                'target' => './tests/build/target',
                'expectedGeneratedTestDataCollection' => [
                    [
                        'classNameReplacement' => 'IndexPageOpenTest',
                        'expectedContentPath' => '/tests/Fixtures/php/Test-Integration/IndexPageOpenTest.php',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param string $source
     * @param string $target
     *
     * @return Process<string>
     */
    private function createGenerateCommandProcess(string $source, string $target): Process
    {
        return new Process([
            './bin/compiler',
            '--source=' . $source,
            '--target=' . $target,
            '--base-class=' . AbstractGeneratedTestCase::class
        ]);
    }

    private function replaceGeneratedTestClassName(string $generatedTestContent, string $className): string
    {
        return (string) preg_replace(
            '/class Generated[a-zA-Z0-9]{32}Test/',
            'class ' . $className,
            $generatedTestContent
        );
    }

    private function removeProjectRootPathInGeneratedTest(string $generatedTestContent): string
    {
        return str_replace(
            (new ProjectRootPathProvider())->get(),
            '',
            $generatedTestContent
        );
    }
}
