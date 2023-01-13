<?php

declare(strict_types=1);

namespace SlimAPI\Validation;

class Mapper
{
    private bool|string|null $value = null;

    public function __construct(bool|string|null $value)
    {
        $this->value = $value;
    }

    public function skipValidation(): bool
    {
        return $this->value === false;
    }

    public function getValue(): bool|string|null
    {
        return $this->value;
    }
}
