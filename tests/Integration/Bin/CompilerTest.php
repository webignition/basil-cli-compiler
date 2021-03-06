<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Integration\Bin;

use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use webignition\BasilCliCompiler\Tests\Integration\AbstractGeneratedTestCase;
use webignition\BasilCompilerModels\SuiteManifest;

class CompilerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider generateDataProvider
     *
     * @param array<mixed> $expectedGeneratedTestDataCollection
     */
    public function testGenerate(string $source, string $target, array $expectedGeneratedTestDataCollection): void
    {
        $generateProcess = $this->createGenerateCommandProcess($source, $target);
        $exitCode = $generateProcess->run();

        $this->assertSame(0, $exitCode);

        $suiteManifest = SuiteManifest::fromArray((array) Yaml::parse($generateProcess->getOutput()));

        $testManifests = $suiteManifest->getTestManifests();
        self::assertNotEmpty($testManifests);

        foreach ($testManifests as $index => $testManifest) {
            $testPath = $testManifest->getTarget();
            self::assertFileExists($testPath);

            $expectedGeneratedTestData = $expectedGeneratedTestDataCollection[$index];

            $generatedTestContent = (string) file_get_contents($testPath);

            $classNameReplacement = $expectedGeneratedTestData['classNameReplacement'];
            $generatedTestContent = $this->replaceGeneratedTestClassName($generatedTestContent, $classNameReplacement);
            $generatedTestContent = $this->removeProjectRootPathInGeneratedTest($generatedTestContent);

            $expectedTestContentPath = getcwd() . '/' . $expectedGeneratedTestData['expectedContentPath'];
            $expectedTestContent = (string) file_get_contents($expectedTestContentPath);

            $this->assertSame($expectedTestContent, $generatedTestContent);
        }

        foreach ($testManifests as $testManifest) {
            unlink($testManifest->getTarget());
        }
    }

    /**
     * @return array[]
     */
    public function generateDataProvider(): array
    {
        $root = getcwd();

        return [
            'single test' => [
                'source' => $root . '/tests/Fixtures/basil-integration/Test/index-page-open.yml',
                'target' => $root . '/tests/build/target',
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
        return str_replace((string) getcwd(), '', $generatedTestContent);
    }
}
