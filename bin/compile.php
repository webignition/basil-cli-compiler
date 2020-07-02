<?php

declare(strict_types=1);

use webignition\SingleCommandApplicationPharBuilder\Builder;

$root = (string) realpath(__DIR__ . '/..');

require $root . '/vendor/autoload.php';

$pharCompiler = new Builder(
    $root,
    'build/compiler.phar',
    'bin/compiler',
    [
        'src',
        'vendor/composer',
        'vendor/myclabs',
        'vendor/php-webdriver',
        'vendor/phpunit/phpunit',
        'vendor/symfony',
        'vendor/webignition',
    ]
);

$pharCompiler->build();
