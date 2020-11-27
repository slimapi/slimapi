<?php

declare(strict_types=1);

namespace SlimAPI\Validation\Validator;

use stdClass;

interface ValidatorInterface
{
    public const CONTENT_TYPE = 'application/json';

    public const TYPE_REQUEST = 'request';
    public const TYPE_RESPONSE = 'response';

    /**
     * @param mixed $data
     * @param stdClass $schema
     * @return bool
     */
    public function isValid($data, stdClass $schema): bool;

    public function getErrorMessage(): string;

    public function getErrors(): array;
}
