<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunSuccess;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCompilerModels\Configuration;
use webignition\BasilCompilerModels\SuiteManifest;
use webignition\BasilCompilerModels\TestManifest;
use webignition\BasilModels\Test\Configuration as TestModelConfiguration;

trait SuccessDataProviderTrait
{
    /**
     * @return array[]
     */
    public function successDataProvider(): array
    {
        $root = getcwd();

        return [
            'single test' => [
                'input' => [
                    '--source' => $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => 0,
                'expectedCommandOutput' => new SuiteManifest(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    [
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                            $root . '/tests/build/target/GeneratedVerifyOpenLiteralChrome.php',
                            1
                        ),
                    ]
                ),
                'expectedGeneratedCodePaths' => [
                    'tests/Fixtures/php/Test/GeneratedVerifyOpenLiteralChrome.php',
                ],
                'classNames' => [
                    'GeneratedVerifyOpenLiteralChrome',
                ],
            ],
            'single test with multiple browsers' => [
                'input' => [
                    '--source' =>
                        $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal-multiple-browsers.yml',
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => 0,
                'expectedCommandOutput' => new SuiteManifest(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal-multiple-browsers.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    [
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal-multiple-browsers.yml',
                            $root . '/tests/build/target/GeneratedVerifyOpenLiteralChrome.php',
                            1
                        ),
                        new TestManifest(
                            new TestModelConfiguration('firefox', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal-multiple-browsers.yml',
                            $root . '/tests/build/target/GeneratedVerifyOpenLiteralFirefox.php',
                            1
                        ),
                    ]
                ),
                'expectedGeneratedCodePaths' => [
                    'tests/Fixtures/php/Test/GeneratedVerifyOpenLiteralChrome.php',
                    'tests/Fixtures/php/Test/GeneratedVerifyOpenLiteralFirefox.php',
                ],
                'classNames' => [
                    'GeneratedVerifyOpenLiteralChrome',
                    'GeneratedVerifyOpenLiteralFirefox',
                ],
            ],
        ];
    }
}
