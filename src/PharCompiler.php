<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler;

use Phar;
use Symfony\Component\Finder\Finder;

class PharCompiler
{
    private string $baseDirectory;
    private string $pharPath;
    private string $alias;
    private string $binPath;

    public function __construct(string $baseDirectory, string $pharPath, string $binPath)
    {
        $this->baseDirectory = $baseDirectory;
        $this->pharPath = $pharPath;
        $this->alias = basename($pharPath);
        $this->binPath = $binPath;
    }

    public function compile(): void
    {
        $phar = new Phar($this->pharPath, 0, $this->alias);
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
        $content = (string) file_get_contents($this->baseDirectory . '/' . $this->binPath);
        $content = (string) preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString($this->binPath, $content);
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
        return
            '#!/usr/bin/env php' . "\n" .
            '<?php' . "\n" .
            "\n" .
            'Phar::mapPhar(\'' . $this->alias . '\');' . "\n" .
            "\n" .
            'require \'phar://' . $this->alias . '/' . $this->binPath . '\';' . "\n" .
            '' . "\n" .
            '__HALT_COMPILER();' . "\n" .
            "\n"
        ;
    }
}
