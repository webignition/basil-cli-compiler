<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Services;

use phpmock\mockery\PHPMockery;
use webignition\BasilCliCompiler\Services\PhpFileCreator;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;

class PhpFileCreatorTest extends AbstractBaseTest
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $outputDirectory
     * @param string $className
     */
    public function testCreate(
        string $outputDirectory,
        string $className,
        string $code,
        string $expectedFilePutContentsFilename,
        string $expectedFilePutContentsData,
        string $expectedCreatedFilename
    ): void {
        PHPMockery::mock('webignition\BasilCliCompiler\Services', 'file_put_contents')
            ->with($expectedFilePutContentsFilename, $expectedFilePutContentsData)
            ->andReturn(strlen($expectedFilePutContentsData));

        $creator = new PhpFileCreator($outputDirectory);
        $createdFileName = $creator->create($className, $code);

        self::assertSame($expectedCreatedFilename, $createdFileName);
    }

    /**
     * @return array[]
     */
    public function createDataProvider(): array
    {
        return [
            'no output directory' => [
                'outputDirectory' => '',
                'className' => 'TestClassName',
                'code' => 'echo "test code";',
                'expectedFilePutContentsFilename' => '/TestClassName.php',
                'expectedFilePutContentsData' => sprintf($this->getPhpFileCreatorTemplate(), 'echo "test code";'),
                'expectedCreatedFilename' => 'TestClassName.php',
            ],
            'has output directory' => [
                'outputDirectory' => '/build',
                'className' => 'TestClassName',
                'code' => 'echo "test code";',
                'expectedFilePutContentsFilename' => '/build/TestClassName.php',
                'expectedFilePutContentsData' => sprintf($this->getPhpFileCreatorTemplate(), 'echo "test code";'),
                'expectedCreatedFilename' => 'TestClassName.php',
            ],
        ];
    }

    private function getPhpFileCreatorTemplate(): string
    {
        return (new \ReflectionClass(PhpFileCreator::class))->getConstant('TEMPLATE');
    }
}
