<?php

declare(strict_types=1);

namespace SlimAPI\Routing;

use Nette\Utils\ArrayHash;
use SlimAPI\Validation\Mapper;

class Settings extends ArrayHash
{
    public const VALIDATION_MAPPER = 'validation_mapper';

    public function getValidationMapper(): Mapper
    {
        return new Mapper($this[self::VALIDATION_MAPPER] ?? null);
    }
}
