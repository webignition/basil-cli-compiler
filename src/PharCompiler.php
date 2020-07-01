<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler;

use Phar;
use Symfony\Component\Finder\Finder;

class PharCompiler
{
    private string $baseDirectory;
    private string $pharPath;

    public function __construct(string $baseDirectory, string $pharPath)
    {
        $this->baseDirectory = $baseDirectory;
        $this->pharPath = $pharPath;
    }

    public function compile(): void
    {
        $phar = new Phar($this->pharPath, 0, 'compiler.phar');
        $phar->startBuffering();

        $this->addBinCompiler($phar);

        $filesIterator = $this->createFilesFinder([
            'src',
            'vendor/composer',
            'vendor/myclabs',
            'vendor/php-webdriver',
            'vendor/phpunit/phpunit',
            'vendor/symfony',
            'vendor/webignition',
        ]);

        $phar->buildFromIterator($filesIterator, $this->baseDirectory);

        $this->addVendorAutoload($phar);

        $phar->setStub($this->createStub());
        $phar->stopBuffering();
    }

    private function addBinCompiler(Phar $phar): void
    {
        $content = (string) file_get_contents(__DIR__ . '/../bin/compiler');
        $content = (string) preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('bin/compiler', $content);
    }

    /**
     * @param string[] $paths
     *
     * @return \Iterator<\SplFileInfo>
     */
    private function createFilesFinder(array $paths): \Iterator
    {
        $finder = new Finder();
        $finder
            ->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->exclude('Tests')
            ->exclude('tests')
            ->exclude('docs')
            ->in($paths);

        return $finder->getIterator();
    }

    private function addVendorAutoload(Phar $phar): void
    {
        $phar->addFile('vendor/autoload.php');
    }

    private function createStub(): string
    {
        return <<< EOF
#!/usr/bin/env php
<?php

Phar::mapPhar('compiler.phar');

require 'phar://compiler.phar/bin/compiler';

__HALT_COMPILER();

EOF;
    }
}
