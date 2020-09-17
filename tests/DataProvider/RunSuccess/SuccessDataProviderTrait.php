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
                            $root . '/tests/build/target/Generated4238ad333014be4c5d99e227b087cc9eTest.php',
                            1
                        ),
                    ]
                ),
                'expectedGeneratedCodePaths' => [
                    'tests/Fixtures/php/Test/Generated4238ad333014be4c5d99e227b087cc9eTest.php',
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
                            $root . '/tests/build/target/Generated4b5acb6ab789aa90fe19904bd0e5f458Test.php',
                            1
                        ),
                        new TestManifest(
                            new TestModelConfiguration('firefox', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal-multiple-browsers.yml',
                            $root . '/tests/build/target/Generated3cac2ec205642b2a6a0cb7c4c2d04c45Test.php',
                            1
                        ),
                    ]
                ),
                'expectedGeneratedCodePaths' => [
                    'tests/Fixtures/php/Test/Generated4b5acb6ab789aa90fe19904bd0e5f458Test.php',
                    'tests/Fixtures/php/Test/Generated3cac2ec205642b2a6a0cb7c4c2d04c45Test.php',
                ],
            ],
        ];
    }
}
