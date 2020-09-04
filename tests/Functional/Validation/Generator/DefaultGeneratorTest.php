<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\Validation\Generator;

use SlimAPI\Exception\InvalidArgumentException;
use SlimAPI\Tests\Functional\TestCase;
use SlimAPI\Validation\Generator\DefaultGenerator;

class DefaultGeneratorTest extends TestCase
{
    public function setUp(): void
    {
        self::cleanup();
    }

    public function testGenerateSchemaList(): void
    {
        $generator = new DefaultGenerator(__DIR__ . '/../fixtures/*.json', self::CACHE_DIR);
        $generator->generateSchemaList();

        $validation = include $generator->getCacheFileName();
        self::assertSame(['method', 'pattern', 'query'], $validation['[GET]/bar/v1/foo{?filter}'][0]->schema->required);
        self::assertSame('response', $validation['[POST]/foo/v1/bar'][1]->meta->type);
    }

    public function testGenerateSchemaListBadMask(): void
    {
        $generator = new DefaultGenerator('/missing-schema', self::CACHE_DIR);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage("Validation schema has not been found. Used mask: '/missing-schema'.");
        $generator->generateSchemaList();
    }

    public function testGenerateSchemaListBadMaskTwo(): void
    {
        $generator = new DefaultGenerator('/var/www', self::CACHE_DIR);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage(sprintf(
            "Cannot read file '/var/www'. Please check %%sourceMask%% parameter for %s",
            DefaultGenerator::class,
        ));
        $generator->generateSchemaList();
    }

    public function testGenerateSchemaListBadCacheDir(): void
    {
        $generator = new DefaultGenerator(__DIR__ . '/../fixtures/*.json', '/bad-cache-dir');

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage(sprintf(
            "Failed to create validation cache-file '/bad-cache-dir/validation.php'. Please check %%cacheDir%% parameter for %s.",
            DefaultGenerator::class,
        ));

        $generator->generateSchemaList();
    }
}
