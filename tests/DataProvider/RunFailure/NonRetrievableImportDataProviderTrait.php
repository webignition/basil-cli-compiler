<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\DataProvider\RunFailure;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\BasilCliCompiler\Services\ErrorOutputFactory;
use webignition\BasilCompilerModels\Configuration;
use webignition\BasilCompilerModels\ErrorOutput;

trait NonRetrievableImportDataProviderTrait
{
    public function nonRetrievableImportDataProvider(): array
    {
        $root = getcwd();

        $pagePath = $root . '/tests/Fixtures/basil/InvalidPage/unparseable.yml';
        $pageAbsolutePath = '' . $pagePath;

        $testPath = $root . '/tests/Fixtures/basil/InvalidTest/import-unparseable-page.yml';
        $testAbsolutePath = '' . $testPath;

        return [
            'test imports non-parsable page' => [
                'input' => [
                    '--source' => $testPath,
                    '--target' => $root . '/tests/build/target',
                ],
                'expectedExitCode' => ErrorOutputFactory::CODE_LOADER_NON_RETRIEVABLE_IMPORT,
                'expectedCommandOutput' => new ErrorOutput(
                    new Configuration(
                        $testAbsolutePath,
                        $root . '/tests/build/target',
                        AbstractBaseTest::class
                    ),
                    'Cannot retrieve page "unparseable_page" from "' . $pageAbsolutePath . '"',
                    ErrorOutputFactory::CODE_LOADER_NON_RETRIEVABLE_IMPORT,
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
