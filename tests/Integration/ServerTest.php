<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Tests\Integration;

class ServerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider queryServerDataProvider
     */
    public function testQueryServer(string $remoteCommand, int $expectedRemoteCommandExitCode)
    {
        $netcatCommand = '(echo "' . $remoteCommand . '"; sleep 1; echo "quit") | netcat localhost 10000';

        $output = [];
        $commandExitCode = null;
        exec($netcatCommand, $output, $commandExitCode);

        self::assertSame(0, $commandExitCode);

        $responseExitCode = ((int) $output[0]) ?? -1;
        self::assertSame($expectedRemoteCommandExitCode, $responseExitCode);
    }

    public function queryServerDataProvider(): array
    {
        return [
            'get version' => [
                'remoteCommand' => './bin/compiler --version',
                'expectedRemoteCommandExitCode' => 0,
            ],
            'generate' => [
                'remoteCommand' => './bin/compiler --source=tests/Fixtures/basil/Test --target=tests/build/target',
                'expectedRemoteCommandExitCode' => 0,
            ],
            'generate failed; target missing' => [
                'command' => './bin/compiler --source=tests/Fixtures/basil/Test',
                'expectedRemoteCommandExitCode' => 103,
            ],
        ];
    }
}
