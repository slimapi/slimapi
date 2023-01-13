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
        $schema = json_decode('{"properties":{"one":{"type":"array","items":[{"type":"string"}]}}}');
        $data = json_decode('{"two":[42]}');

        $validator = new JsonSchemaValidator();
        self::assertFalse($validator->isValid($data, $schema));

        self::assertSame(
            [
                [
                    'property' => '',
                    'message' => 'The property two is not defined and the definition does not allow additional properties',
                    'constraint' => 'additionalProp',
                ],
            ],
            $validator->getErrors(),
        );
    }

    public function testGetErrorsWithoutCallingIsValid(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Missing method call SlimAPI\Validation\Validator\JsonSchemaValidator:isValid().');
        (new JsonSchemaValidator())->getErrors();
    }
}
