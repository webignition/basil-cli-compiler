<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunSuccess;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Model\SuccessOutput;
use webignition\BasilCliCompiler\Model\TestManifest;
use webignition\BasilCliCompiler\Tests\Services\ProjectRootPathProvider;
use webignition\BasilModels\Test\Configuration as TestModelConfiguration;

trait SuccessDataProviderTrait
{
    public function successDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();

        return [
            'single test' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => 0,
                'expectedCommandOutput' => new SuccessOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    [
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                            'Generated0233b88be49ad918bec797dcba9b01afTest.php'
                        ),
                    ]
                ),
                'expectedGeneratedCode' => [
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/Generated0233b88be49ad918bec797dcba9b01afTest.php')
                ],
            ],
            'test suite' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/TestSuite/example.com-all.yml',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => 0,
                'expectedCommandOutput' => new SuccessOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/TestSuite/example.com-all.yml',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    [
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                            'Generated0233b88be49ad918bec797dcba9b01afTest.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            'tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml',
                            'Generated641755df3ae8af9eb1cd971239e161fbTest.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            'tests/Fixtures/basil/Test/example.com.follow-more-information.yml',
                            'Generated1a8ee6813e6fc3bf6de1ddbb4aaf6115Test.php'
                        ),
                    ]
                ),
                'expectedGeneratedCode' => [
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/Generated0233b88be49ad918bec797dcba9b01afTest.php'),
                    'tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/Generated641755df3ae8af9eb1cd971239e161fbTest.php'),
                    'tests/Fixtures/basil/Test/example.com.follow-more-information.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/Generated1a8ee6813e6fc3bf6de1ddbb4aaf6115Test.php')
                ],
            ],
            'collection of tests by directory' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/Test',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => 0,
                'expectedCommandOutput' => new SuccessOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/Test',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    [
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            'tests/Fixtures/basil/Test/example.com.follow-more-information.yml',
                            'Generated1a8ee6813e6fc3bf6de1ddbb4aaf6115Test.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            'tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml',
                            'Generated641755df3ae8af9eb1cd971239e161fbTest.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            'tests/Fixtures/basil/Test/example.com.verify-open-literal-data-sets.yml',
                            'Generated6a67c4998bdf379738159830570c8ebeTest.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                            'Generated0233b88be49ad918bec797dcba9b01afTest.php'
                        ),
                    ]
                ),
                'expectedGeneratedCode' => [
                    'tests/Fixtures/basil/Test/example.com.follow-more-information.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/Generated1a8ee6813e6fc3bf6de1ddbb4aaf6115Test.php'),
                    'tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/Generated641755df3ae8af9eb1cd971239e161fbTest.php'),
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal-data-sets.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/Generated6a67c4998bdf379738159830570c8ebeTest.php'),
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/Generated0233b88be49ad918bec797dcba9b01afTest.php'),
                ],
            ],
            'collection of test suites by directory' => [
                'input' => [
                    '--source' => 'tests/Fixtures/basil/TestSuite',
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => 0,
                'expectedCommandOutput' => new SuccessOutput(
                    new Configuration(
                        $root . '/tests/Fixtures/basil/TestSuite',
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    [
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                            'Generated0233b88be49ad918bec797dcba9b01afTest.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            'tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml',
                            'Generated641755df3ae8af9eb1cd971239e161fbTest.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            'tests/Fixtures/basil/Test/example.com.follow-more-information.yml',
                            'Generated1a8ee6813e6fc3bf6de1ddbb4aaf6115Test.php'
                        ),
                        new TestManifest(
                            new TestModelConfiguration('chrome', 'https://example.com/'),
                            'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml',
                            'Generated0233b88be49ad918bec797dcba9b01afTest.php'
                        ),
                    ]
                ),
                'expectedGeneratedCode' => [
                    'tests/Fixtures/basil/Test/example.com.verify-open-literal.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/Generated0233b88be49ad918bec797dcba9b01afTest.php'),
                    'tests/Fixtures/basil/Test/example.com.import-step-verify-open-literal.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/Generated641755df3ae8af9eb1cd971239e161fbTest.php'),
                    'tests/Fixtures/basil/Test/example.com.follow-more-information.yml' =>
                        file_get_contents('tests/Fixtures/php/Test/Generated1a8ee6813e6fc3bf6de1ddbb4aaf6115Test.php'),
                ],
            ],
        ];
    }
}
