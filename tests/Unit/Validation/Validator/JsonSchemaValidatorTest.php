<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Unit\Validation\Validator;

use SlimAPI\Exception\LogicException;
use SlimAPI\Tests\TestCase;
use SlimAPI\Validation\Validator\JsonSchemaValidator;

class JsonSchemaValidatorTest extends TestCase
{
    public function testIsValidSuccess(): void
    {
        $schema = json_decode('{"properties":{"propertyOne":{"type":"array","items":[{"type":"string"}]}}}');
        $data = json_decode('{"propertyOne":["42"]}');

        $validator = new JsonSchemaValidator();
        self::assertTrue($validator->isValid($data, $schema));
    }

    public function testStrictMode(): void
    {
        $schema = json_decode('{"properties":{"propertyOne":{"type":"array","items":[{"type":"string"}]}}}');
        $data = json_decode('{"propertyTwo":[42]}');

        $validator = new JsonSchemaValidator();
        self::assertFalse($validator->isValid($data, $schema));

        self::assertSame(
            '["The property propertyTwo is not defined and the definition does not allow additional properties"]',
            $validator->generateErrorMessage(),
        );
    }

    public function testGenerateErrorMessageWithoutCallingIsValid(): void
    {
        self::expectException(LogicException::class);
        self::expectExceptionMessage('Missing method call SlimAPI\Validation\Validator\JsonSchemaValidator:isValid().');
        (new JsonSchemaValidator())->generateErrorMessage();
    }
}
