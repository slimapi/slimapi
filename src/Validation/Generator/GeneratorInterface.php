<?php

declare(strict_types=1);

namespace SlimAPI\Validation\Generator;

interface GeneratorInterface
{
    public function generateSchemaList(): array;

    public function getCacheFileName(): string;

    public function getSchemaList(): array;
}
