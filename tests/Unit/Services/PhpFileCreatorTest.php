<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Services;

use phpmock\mockery\PHPMockery;
use webignition\BasilCliCompiler\Services\PhpFileCreator;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;
use webignition\ObjectReflector\ObjectReflector;

class PhpFileCreatorTest extends AbstractBaseTest
{
    public function testSetOutputDirectory()
    {
        $creator = new PhpFileCreator();
        self::assertSame('', ObjectReflector::getProperty($creator, 'outputDirectory'));

        $outputDirectory = 'build';
        $creator->setOutputDirectory($outputDirectory);
        self::assertSame($outputDirectory, ObjectReflector::getProperty($creator, 'outputDirectory'));
    }

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
    ) {
        PHPMockery::mock('webignition\BasilCliCompiler\Services', 'file_put_contents')
            ->with($expectedFilePutContentsFilename, $expectedFilePutContentsData)
            ->andReturn(strlen($expectedFilePutContentsData));

        $creator = new PhpFileCreator();
        $creator->setOutputDirectory($outputDirectory);
        $createdFileName = $creator->create($className, $code);

        self::assertSame($expectedCreatedFilename, $createdFileName);
    }

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
