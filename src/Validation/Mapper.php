<?php

declare(strict_types=1);

namespace SlimAPI\Validation;

class Mapper
{
    /** @var bool|string|null */
    private $value;

    /**
     * @param bool|string|null $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function skipValidation(): bool
    {
        return $this->value === false;
    }

    /**
     * @return bool|string|null
     */
    public function getValue()
    {
        return $this->value;
    }
}
