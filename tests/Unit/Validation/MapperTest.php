<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Unit\Validation;

use SlimAPI\Tests\TestCase;
use SlimAPI\Validation\Mapper;

class MapperTest extends TestCase
{
    public function testSkipValidation(): void
    {
        $mapper = new Mapper(false);
        self::assertTrue($mapper->skipValidation());
    }
}
