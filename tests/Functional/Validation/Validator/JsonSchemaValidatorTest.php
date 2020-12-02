<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\Validation\Validator;

use SlimAPI\App;
use SlimAPI\Exception\Validation\RequestException;
use SlimAPI\Tests\Functional\TestCase;

class JsonSchemaValidatorTest extends TestCase
{
    public function setUp(): void
    {
        self::cleanup();
    }

    public function testSuccessWhenStrictModeOn(): void
    {
        $container = self::createContainer(__DIR__ . '/../fixtures/validation.neon');
        $application = $container->getByType(App::class);

        $response = $application->handle($this->createRequestPost('/foo/v1/bar', ['id' => 123], []));
        $data = $response->getJson(true);

        self::assertSame('POST', $data['method']);
        self::assertSame('/foo/v1/bar', $data['pattern']);
        self::assertCount(2, $data['validation']);
    }

    public function testFailWhenStrictModeOn(): void
    {
        $container = self::createContainer(__DIR__ . '/../fixtures/validation.neon');
        $application = $container->getByType(App::class);

        self::expectException(RequestException::class);
        self::expectExceptionMessage(sprintf(
            '[{"property":"","message":"%s","constraint":"additionalProp"}]',
            'The property fooProperty is not defined and the definition does not allow additional properties',
        ));

        $application->handle($this->createRequestPost('/foo/v1/bar', ['id' => 123, 'fooProperty' => 'bar'], []));
    }

    public function testSuccessWhenStrictModeOff(): void
    {
        $container = self::createContainer(__DIR__ . '/../fixtures/validation_nonstrict.neon');
        $application = $container->getByType(App::class);

        $response = $application->handle($this->createRequestPost('/foo/v1/bar', ['id' => 123, 'fooProperty' => 'bar']));
        $data = $response->getJson(true);

        self::assertSame('POST', $data['method']);
        self::assertSame('/foo/v1/bar', $data['pattern']);
        self::assertCount(2, $data['validation']);
    }
}
