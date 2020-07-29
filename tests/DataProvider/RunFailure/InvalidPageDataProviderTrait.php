<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunFailure;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Tests\Services\ProjectRootPathProvider;
use webignition\BasilCompilerModels\Configuration;
use webignition\BasilCompilerModels\ErrorOutput;

trait InvalidPageDataProviderTrait
{
    public function invalidPageDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();

        $testPath = $root . '/tests/Fixtures/basil/InvalidTest/import-empty-page.yml';
        $testAbsolutePath = '' . $testPath;

        $pagePath = $root . '/tests/Fixtures/basil/InvalidPage/url-empty.yml';
        $pageAbsolutePath = '' . $pagePath;

        $testSuitePath = $root . '/tests/Fixtures/basil/InvalidTestSuite/imports-invalid-page.yml';
        $testSuiteAbsolutePath = '' . $testSuitePath;

        return [
            'test imports invalid page; url empty' => [
                'input' => [
                    '--source' => $testPath,
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_INVALID_PAGE,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $testAbsolutePath,
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Invalid page "empty_url_page" at path "' . $pageAbsolutePath . '": page-url-empty',
                    ErrorOutput::CODE_LOADER_INVALID_PAGE,
                    [
                        'test_path' => $testAbsolutePath,
                        'import_name' => 'empty_url_page',
                        'page_path' => $pageAbsolutePath,
                        'validation_result' => [
                            'type' => 'page',
                            'reason' => 'page-url-empty',
                        ],
                    ]
                ),
            ],
            'test suite imports test which imports invalid page; url empty' => [
                'input' => [
                    '--source' => $testSuitePath,
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_INVALID_PAGE,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $testSuiteAbsolutePath,
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Invalid page "empty_url_page" at path "' . $pageAbsolutePath . '": page-url-empty',
                    ErrorOutput::CODE_LOADER_INVALID_PAGE,
                    [
                        'test_path' => $testAbsolutePath,
                        'import_name' => 'empty_url_page',
                        'page_path' => $pageAbsolutePath,
                        'validation_result' => [
                            'type' => 'page',
                            'reason' => 'page-url-empty',
                        ],
                    ]
                ),
            ],
        ];
    }
}
