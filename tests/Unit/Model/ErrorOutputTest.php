<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Unit\Model;

use webignition\BasilCliCompiler\Model\Configuration;
use webignition\BasilCliCompiler\Model\ErrorOutput;
use webignition\BasilCliCompiler\Tests\Unit\AbstractBaseTest;

class ErrorOutputTest extends AbstractBaseTest
{
    private ErrorOutput $output;
    private Configuration $configuration;
    private string $message = 'message content';
    private int $code = ErrorOutput::CODE_UNKNOWN;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = new Configuration('test.yml', 'build', AbstractBaseTest::class);
        $this->output = new ErrorOutput($this->configuration, $this->message, $this->code);
    }

    public function testGetConfiguration()
    {
        self::assertSame($this->configuration, $this->output->getConfiguration());
    }

    public function testGetCode()
    {
        self::assertSame($this->code, $this->output->getCode());
    }

    public function testGetData()
    {
        self::assertSame(
            [
                'config' => $this->configuration->getData(),
                'status' => 'failure',
                'error' => [
                    'message' => $this->message,
                    'code' => $this->code,
                ],
            ],
            $this->output->getData()
        );
    }

    /**
     * @dataProvider getDataFromArrayDataProvider
     */
    public function testGetDataFromArray(ErrorOutput $output)
    {
        self::assertEquals(
            $output,
            ErrorOutput::fromArray($output->getData())
        );
    }

    public function getDataFromArrayDataProvider(): array
    {
        return [
            'without context' => [
                'output' => new ErrorOutput(
                    new Configuration(
                        'source-value',
                        'target-value',
                        AbstractBaseTest::class
                    ),
                    'error-message-01',
                    1
                ),
            ],
            'with context' => [
                'output' => new ErrorOutput(
                    new Configuration(
                        'source-value',
                        'target-value',
                        AbstractBaseTest::class
                    ),
                    'error-message-01',
                    1,
                    [
                        'context-key-01' => 'context-value-01',
                        'context-key-02' => 'context-value-02',
                    ]
                ),
            ],
        ];
    }
}
