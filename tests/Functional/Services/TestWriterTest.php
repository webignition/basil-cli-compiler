<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Functional\Services;

use webignition\BasilCliCompiler\Model\CompiledTest;
use webignition\BasilCliCompiler\Services\TestWriter;
use webignition\BasilModels\Test\ConfigurationInterface;
use webignition\BasilModels\Test\TestInterface;

class TestWriterTest extends \PHPUnit\Framework\TestCase
{
    private TestWriter $testWriter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testWriter = TestWriter::createWriter();
    }

    /**
     * @dataProvider generateDataProvider
     */
    public function testWrite(
        CompiledTest $compiledTest,
        string $outputDirectory,
        string $expectedGeneratedCode
    ) {
        $target = $this->testWriter->write($compiledTest, $outputDirectory);

        self::assertFileExists($target);
        self::assertFileIsReadable($target);

        self::assertEquals($expectedGeneratedCode, file_get_contents($target));

        if (file_exists($target)) {
            unlink($target);
        }
    }

    public function generateDataProvider(): array
    {
        $root = getcwd();

        $testConfiguration = \Mockery::mock(ConfigurationInterface::class);

        $test = \Mockery::mock(TestInterface::class);
        $test
            ->shouldReceive('getPath')
            ->andReturn('test.yml');

        $test
            ->shouldReceive('getConfiguration')
            ->andReturn($testConfiguration);

        return [
            'default' => [
                'compiledTest' => new CompiledTest(
                    $test,
                    'compiled test code',
                    'ClassName'
                ),
                'outputDirectory' => $root . '/tests/build/target',
                'expectedGeneratedCode' =>
                    '<?php' . "\n" .
                    "\n" .
                    'namespace webignition\BasilCliCompiler\Generated;' . "\n" .
                    "\n" .
                    'compiled test code' . "\n",
            ],
        ];
    }
}
