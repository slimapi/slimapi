<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\Validation;

use SlimAPI\Tests\TestCase;
use SlimAPI\Validation\Command;
use SlimAPI\Validation\Generator\DefaultGenerator;
use Symfony\Component\Console\Tester\CommandTester;

class CommandTest extends TestCase
{
    public function testExecute(): void
    {
        self::cleanup();
        $generator = new DefaultGenerator(__DIR__ . '/fixtures/validation.bar.json', self::CACHE_DIR);

        $command = new Command($generator);

        $tester = new CommandTester($command);
        $tester->execute([]);

        $cacheFile = $generator->getCacheFileName();
        $validation = include_once $cacheFile;
        self::assertCount(5, $validation, 'Expected only validation.bar.json');

        self::assertSame(0, $tester->getStatusCode());
        self::assertSame(sprintf('Validation schema (%s) has been generated.', $cacheFile), trim($tester->getDisplay()));
    }
}
