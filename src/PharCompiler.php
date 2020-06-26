<?php

declare(strict_types=1);

namespace webignition\BasilCliCompiler;

use Phar;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class PharCompiler
{
    public const DEFAULT_PHAR_FILENAME = 'build/compiler.phar';
    private const COMPILER_PATH = 'bin/compiler';

    public function compile(string $pharFile = self::DEFAULT_PHAR_FILENAME): void
    {
        $phar = new Phar($pharFile, 0, 'compiler.phar');
        $phar->startBuffering();

        $this->addSrc($phar);
        $this->addDependenciesByDirectory($phar, [
            'vendor/symfony',
            'vendor/webignition',
        ]);
        $this->addVendorAutoload($phar);

        $phar->setStub($this->createStubFromBinCompiler());
        $phar->stopBuffering();
    }

    private function createStubFromBinCompiler(): string
    {
        $stubBody = (string) file_get_contents(self::COMPILER_PATH);
        $stubBodyLines = explode("\n", $stubBody);
        array_shift($stubBodyLines);
        $stubBody = implode("\n", $stubBodyLines);

        return $stubBody . "\n" . '__HALT_COMPILER();';
    }

    private function addSrc(Phar $phar): void
    {
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->notName('PharCompiler.php')
            ->in('src')
        ;

        foreach ($finder as $file) {
            $phar->addFile($file->getPathname());
        }
    }

    /**
     * @param Phar $phar
     * @param string[] $paths
     */
    private function addDependenciesByDirectory(Phar $phar, array $paths): void
    {
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->exclude('Tests')
            ->exclude('tests')
            ->exclude('docs')
            ->in($paths)
        ;

        foreach ($finder as $file) {
            $phar->addFile($file->getPathname());
        }
    }

    private function addVendorAutoload(Phar $phar): void
    {
        $vendorAutoloadFile = new SplFileInfo(
            'vendor/autoload.php',
            'vendor',
            'vendor/autoload.php'
        );
        $phar->addFile($vendorAutoloadFile->getPathname());
    }
}
