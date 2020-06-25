<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\DI;

use SlimAPI\DI\ContainerAdapter;
use SlimAPI\Http\Request;
use SlimAPI\Tests\TestCase;

class ContainerAdapterTest extends TestCase
{
    public function testServices(): void
    {
        $container = self::createContainer(__FIXTURES_DIR__ . '/config.neon');

        /** @var ContainerAdapter $adapter */
        $adapter = $container->getService('slimapi.container');
        self::assertInstanceOf(Request::class, $adapter->get('request'));
        self::assertTrue($adapter->has('request'));
        self::assertTrue($adapter->hasService('slimapi.request'));
    }
}
