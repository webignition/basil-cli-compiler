<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Integration\Bin;

use webignition\BasilCliCompiler\Model\SuccessOutput;
use webignition\BasilCliCompiler\Services\ProjectRootPathProvider;
use webignition\BasilCliCompiler\Tests\Integration\AbstractGeneratedTestCase;

class CompilerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider generateAndRunDataProvider
     *
     * @param string $source
     * @param string $target
     * @param array<mixed> $expectedGeneratedTestDataCollection
     */
    public function testGenerateAndRun(
        string $source,
        string $target,
        array $expectedGeneratedTestDataCollection
    ) {
        $projectRootPath = (new ProjectRootPathProvider())->get();

        $generateCommand = $this->createGenerateCommand($source, $target);
        $generateCommandOutput = SuccessOutput::fromJson((string) shell_exec($generateCommand));

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

    public function generateAndRunDataProvider(): array
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

    private function createGenerateCommand(string $source, string $target): string
    {
        return './bin/compiler ' .
            '--source=' . $source . ' ' .
            '--target=' . $target . ' ' .
            '--base-class="' . AbstractGeneratedTestCase::class . '"';
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
