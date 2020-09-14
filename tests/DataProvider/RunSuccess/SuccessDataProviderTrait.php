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
                            $root . '/tests/build/target/Generated4238ad333014be4c5d99e227b087cc9eTest.php'
                        ),
                    ]
                ),
                'expectedGeneratedCodePaths' => [
                    'tests/Fixtures/php/Test/Generated4238ad333014be4c5d99e227b087cc9eTest.php',
                ],
            ],
            'test suite' => [
                'input' => [
                    '--source' => $root . '/tests/Fixtures/basil/TestSuite/example.com-all.yml',
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => 0,
                'expectedCommandOutput' => new SuiteManifest(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/TestSuite/example.com-all.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    [
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                            $root . '/tests/build/target/Generated4238ad333014be4c5d99e227b087cc9eTest.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml',
                            $root . '/tests/build/target/Generated0c65b0d5e0e28f11ea3c8193ad9d162dTest.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.follow-more-information.yml',
                            $root . '/tests/build/target/Generated462f42149b07d13071c2620afb561b30Test.php'
                        ),
                    ]
                ),
                'expectedGeneratedCodePaths' => [
                    'tests/Fixtures/php/Test/Generated4238ad333014be4c5d99e227b087cc9eTest.php',
                    'tests/Fixtures/php/Test/Generated0c65b0d5e0e28f11ea3c8193ad9d162dTest.php',
                    'tests/Fixtures/php/Test/Generated462f42149b07d13071c2620afb561b30Test.php',
                ],
            ],
            'collection of tests by directory' => [
                'input' => [
                    '--source' => $root . '/tests/Fixtures/basil/Test',
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => 0,
                'expectedCommandOutput' => new SuiteManifest(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/Test',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    [
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.follow-more-information.yml',
                            $root . '/tests/build/target/Generated462f42149b07d13071c2620afb561b30Test.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml',
                            $root . '/tests/build/target/Generated0c65b0d5e0e28f11ea3c8193ad9d162dTest.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal-data-sets.yml',
                            $root . '/tests/build/target/GeneratedAe219ca93cea5924090e0b5a2d2eea22Test.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal-multiple-browsers.yml',
                            $root . '/tests/build/target/Generated4b5acb6ab789aa90fe19904bd0e5f458Test.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('firefox', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal-multiple-browsers.yml',
                            $root . '/tests/build/target/Generated3cac2ec205642b2a6a0cb7c4c2d04c45Test.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                            $root . '/tests/build/target/Generated4238ad333014be4c5d99e227b087cc9eTest.php'
                        ),
                    ]
                ),
                'expectedGeneratedCodePaths' => [
                    'tests/Fixtures/php/Test/Generated462f42149b07d13071c2620afb561b30Test.php',
                    'tests/Fixtures/php/Test/Generated0c65b0d5e0e28f11ea3c8193ad9d162dTest.php',
                    'tests/Fixtures/php/Test/GeneratedAe219ca93cea5924090e0b5a2d2eea22Test.php',
                    'tests/Fixtures/php/Test/Generated4b5acb6ab789aa90fe19904bd0e5f458Test.php',
                    'tests/Fixtures/php/Test/Generated3cac2ec205642b2a6a0cb7c4c2d04c45Test.php',
                    'tests/Fixtures/php/Test/Generated4238ad333014be4c5d99e227b087cc9eTest.php',
                ],
            ],
            'collection of test suites by directory' => [
                'input' => [
                    '--source' => $root . '/tests/Fixtures/basil/TestSuite',
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => 0,
                'expectedCommandOutput' => new SuiteManifest(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/TestSuite',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    [
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                            $root . '/tests/build/target/Generated4238ad333014be4c5d99e227b087cc9eTest.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml',
                            $root . '/tests/build/target/Generated0c65b0d5e0e28f11ea3c8193ad9d162dTest.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.follow-more-information.yml',
                            $root . '/tests/build/target/Generated462f42149b07d13071c2620afb561b30Test.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                            $root . '/tests/build/target/Generated4238ad333014be4c5d99e227b087cc9eTest.php'
                        ),
                    ]
                ),
                'expectedGeneratedCodePaths' => [
                    'tests/Fixtures/php/Test/Generated4238ad333014be4c5d99e227b087cc9eTest.php',
                    'tests/Fixtures/php/Test/Generated0c65b0d5e0e28f11ea3c8193ad9d162dTest.php',
                    'tests/Fixtures/php/Test/Generated462f42149b07d13071c2620afb561b30Test.php',
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
                            $root . '/tests/build/target/Generated4b5acb6ab789aa90fe19904bd0e5f458Test.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('firefox', 'https://example.com/'),
                            $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal-multiple-browsers.yml',
                            $root . '/tests/build/target/Generated3cac2ec205642b2a6a0cb7c4c2d04c45Test.php'
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
