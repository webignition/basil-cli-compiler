<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunFailure;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Model\ErrorOutput;
use webignition\BasilCliCompiler\Services\ProjectRootPathProvider;

trait NonRetrievableImportDataProviderTrait
{
    public function nonRetrievableImportDataProvider(): array
    {
        $root = (new ProjectRootPathProvider())->get();

        $pagePath = $root . '/tests/Fixtures/basil/InvalidPage/unparseable.yml';
        $pageAbsolutePath = '' . $pagePath;

        $testPath = $root . '/tests/Fixtures/basil/InvalidTest/import-unparseable-page.yml';
        $testAbsolutePath = '' . $testPath;

        $testSuitePath = $root . '/tests/Fixtures/basil/InvalidTestSuite/imports-unparseable-page.yml';
        $testSuiteAbsolutePath = '' . $testSuitePath;

        return [
            'test imports non-parsable page' => [
                'input' => [
                    '--source' => $testPath,
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_NON_RETRIEVABLE_IMPORT,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $testAbsolutePath,
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Cannot retrieve page "unparseable_page" from "' . $pageAbsolutePath . '"',
                    ErrorOutput::CODE_LOADER_NON_RETRIEVABLE_IMPORT,
                    [
                        'test_path' => $testAbsolutePath,
                        'type' => 'page',
                        'name' => 'unparseable_page',
                        'import_path' => $pageAbsolutePath,
                        'loader_error' => [
                            'message' => 'Malformed inline YAML string: ""http://example.com" at line 2.',
                            'path' => $pageAbsolutePath,
                        ],
                    ]
                ),
            ],
            'test suite imports test which imports non-parsable page' => [
                'input' => [
                    '--source' => $testSuiteAbsolutePath,
                    '--target' => 'tests/build/target',
                ],
                'expectedExitCode' => ErrorOutput::CODE_LOADER_NON_RETRIEVABLE_IMPORT,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $testSuiteAbsolutePath,
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Cannot retrieve page "unparseable_page" from "' . $pageAbsolutePath . '"',
                    ErrorOutput::CODE_LOADER_NON_RETRIEVABLE_IMPORT,
                    [
                        'test_path' => $testAbsolutePath,
                        'type' => 'page',
                        'name' => 'unparseable_page',
                        'import_path' => $pageAbsolutePath,
                        'loader_error' => [
                            'message' => 'Malformed inline YAML string: ""http://example.com" at line 2.',
                            'path' => $pageAbsolutePath,
                        ],
                    ]
                ),
            ],
        ];
    }
}
