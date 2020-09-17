<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler\Services;

class PhpFileCreator
{
    private const TEMPLATE = <<< 'EOT'
<?php

namespace webignition\BasilCliCompiler\Generated;

%s

EOT;


    private string $outputDirectory = '';

    public function __construct(string $outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;
    }

    public function create(string $className, string $code): string
    {
        $content = sprintf(self::TEMPLATE, $code);

        $filename = $className . '.php';
        $path = $this->outputDirectory . '/' . $filename;

        file_put_contents($path, $content);

        return $filename;
    }
}
