<?php

declare(strict_types=1);

namespace SlimAPI\Validation\Validator;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use SlimAPI\Exception\LogicException;
use stdClass;

class JsonSchemaValidator implements ValidatorInterface
{
    public const DEFAULT_STRICT = true;

    private bool $strict;

    private ?Validator $validator = null;

    /**
     * @param bool $strict The JSON Schema "additionalProperties: false" will be used in every schema.
     */
    public function __construct(bool $strict = self::DEFAULT_STRICT)
    {
        $this->strict = $strict;
    }

    public function isValid(mixed $data, stdClass $schema): bool
    {
        $this->validator = new Validator();

        if ($this->strict) {
            $schema->additionalProperties = false;
        }

        $this->validator->validate(
            $data,
            $schema,
            Constraint::CHECK_MODE_VALIDATE_SCHEMA,
        );

        return $this->validator->isValid();
    }

    public function getErrorMessage(): string
    {
        return 'JSON Schema validation error.';
    }

    public function getErrors(): array
    {
        if ($this->validator === null) {
            throw new LogicException(sprintf('Missing method call %s:%s().', self::class, 'isValid'));
        }

        $errors = [];
        foreach ($this->validator->getErrors() as $e) {
            unset($e['pointer'], $e['context']);
            $errors[] = $e;
        }

        return $errors;
    }
}
