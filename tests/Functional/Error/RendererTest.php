<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\Error;

use LogicException;
use PHPUnit\Framework\Assert;
use Slim\Exception\HttpBadRequestException;
use SlimAPI\App;
use SlimAPI\Exception\Http\UnprocessableEntityException;
use SlimAPI\Exception\Validation\RequestException;
use SlimAPI\Http\Request;
use SlimAPI\Http\Response;
use SlimAPI\Tests\Functional\Error\fixtures\TestLogger;
use SlimAPI\Tests\Functional\TestCase;
use SlimAPI\Validation\Validator\JsonSchemaValidator;

class RendererTest extends TestCase
{
    protected static App $application;

    public function setUp(): void
    {
        TestLogger::$records = [];
    }

    public function testSlimHttpException(): void
    {
        $response = $this->doRequest('/error/slim-http');

        $expectedCode = 400;
        $expectedMessage = 'Bad request.';
        $expectedError = 'BAD_REQUEST';

        // Response asserts
        $this->assertBaseErrorStrucuture(
            $response->getJson(true),
            $expectedCode,
            $expectedError,
            $expectedMessage,
        );

        // Logger asserts
        $this->assertLoggerStructure(TestLogger::$records, $expectedMessage);
        $this->assertDetailedErrorStructure(
            TestLogger::$records['error'][0][1]['error'],
            $expectedCode,
            $expectedError,
            $expectedMessage,
        );
        $this->assertExceptionStructure(
            TestLogger::$records['error'][0][1]['error']['exception'],
            $expectedCode,
            '/var/www/tests/Functional/Error/RendererTest.php',
            $expectedMessage,
            'Slim\Exception\HttpBadRequestException',
            'RendererTest->actionHandler',
        );
    }

    // phpcs:ignore SlevomatCodingStandard.Functions.FunctionLength
    public function testValidationRequestException(): void
    {
        $response = $this->doRequest('/error/validation-request');

        $expectedCode = 400;
        $expectedMessage = 'JSON Schema validation error.';
        $expectedError = 'VALIDATION_ERROR';
        $expectedValidationError = [
            [
                'property' => 'zip',
                'message' => 'String value found, but a number is required',
                'constraint' => 'type',
            ],
        ];

        // Response asserts
        $data = $response->getJson(true);
        $this->assertErrorStructure(
            $data,
            $expectedCode,
            $expectedError,
            $expectedMessage,
        );
        $validationErrorAttributes = ['code', 'error', 'validation', 'message', 'id'];
        Assert::assertSame($validationErrorAttributes, array_keys($data));
        Assert::assertEquals($expectedValidationError, $data['validation']);

        // Logger asserts
        $expectedLoggerMessage = (string) json_encode($expectedValidationError);
        $this->assertLoggerStructure(TestLogger::$records, $expectedLoggerMessage);
        Assert::assertSame(
            array_merge($validationErrorAttributes, ['exception']),
            array_keys(TestLogger::$records['error'][0][1]['error']),
        );
        $this->assertErrorStructure(
            TestLogger::$records['error'][0][1]['error'],
            $expectedCode,
            $expectedError,
            $expectedMessage,
        );
        $this->assertExceptionStructure(
            TestLogger::$records['error'][0][1]['error']['exception'],
            $expectedCode,
            '/var/www/tests/Functional/Error/RendererTest.php',
            $expectedLoggerMessage,
            'SlimAPI\Exception\Validation\RequestException',
            'RendererTest->actionHandler',
        );
    }

    public function testSlimApiHttpException(): void
    {
        $response = $this->doRequest('/error/slimapi-http');

        $expectedCode = 422;
        $expectedMessage = 'Inactive client.';
        $expectedError = 'INACTIVE_CLIENT';

        // Response asserts
        $this->assertBaseErrorStrucuture(
            $response->getJson(true),
            $expectedCode,
            $expectedError,
            $expectedMessage,
        );

        // Logger asserts
        $this->assertLoggerStructure(TestLogger::$records, $expectedMessage);
        $this->assertDetailedErrorStructure(
            TestLogger::$records['error'][0][1]['error'],
            $expectedCode,
            $expectedError,
            $expectedMessage,
        );
        $this->assertExceptionStructure(
            TestLogger::$records['error'][0][1]['error']['exception'],
            $expectedCode,
            '/var/www/tests/Functional/Error/RendererTest.php',
            $expectedMessage,
            'SlimAPI\Exception\Http\UnprocessableEntityException',
            'RendererTest->actionHandler',
        );
    }

    public function testUnexpectedException(): void
    {
        $response = $this->doRequest('/error/unexpeced');

        $expectedCode = 500;
        $expectedMessage = 'SlimAPI application error.';
        $expectedError = 'INTERNAL_SERVER_ERROR';

        // Response asserts
        $this->assertBaseErrorStrucuture(
            $response->getJson(true),
            $expectedCode,
            $expectedError,
            $expectedMessage,
        );

        // Logger asserts
        $expectedRealExceptionMessage = 'This is something unexpected!';
        $this->assertLoggerStructure(TestLogger::$records, $expectedRealExceptionMessage);
        $this->assertDetailedErrorStructure(
            TestLogger::$records['error'][0][1]['error'],
            $expectedCode,
            $expectedError,
            $expectedMessage,
        );
        $this->assertExceptionStructure(
            TestLogger::$records['error'][0][1]['error']['exception'],
            0,
            '/var/www/tests/Functional/Error/RendererTest.php',
            $expectedRealExceptionMessage,
            'LogicException',
            'RendererTest->actionHandler',
        );
    }

    public function testDetailedErrors(): void
    {
        $container = self::createContainer(__DIR__ . '/fixtures/error_detail.neon');
        $application = $container->getByType(App::class);

        $request = $this->createRequestGet('/error/unexpeced');
        $request = $request->withHeader('Accept', 'application/json');
        $response = $application->handle($request);

        // Response asserts
        $data = $response->getJson(true);
        $this->assertDetailedErrorStructure(
            $data,
            500,
            'INTERNAL_SERVER_ERROR',
            'SlimAPI application error.',
        );
        $this->assertExceptionStructure(
            $data['exception'],
            0,
            '/var/www/tests/Functional/Error/RendererTest.php',
            'This is something unexpected!',
            'LogicException',
            'RendererTest->actionHandler',
        );

        // Logger asserts
        $this->assertLoggerStructure(TestLogger::$records, 'This is something unexpected!');
        $this->assertDetailedErrorStructure(
            TestLogger::$records['error'][0][1]['error'],
            500,
            'INTERNAL_SERVER_ERROR',
            'SlimAPI application error.',
        );
        $this->assertExceptionStructure(
            TestLogger::$records['error'][0][1]['error']['exception'],
            0,
            '/var/www/tests/Functional/Error/RendererTest.php',
            'This is something unexpected!',
            'LogicException',
            'RendererTest->actionHandler',
        );
    }

    public function actionHandler(Request $request): void
    {
        switch ((string) $request->getUri()) {
            case '/error/slim-http':
                throw new HttpBadRequestException($request);

            case '/error/slimapi-http':
                throw new UnprocessableEntityException('INACTIVE_CLIENT');

            case '/error/validation-request':
                $schema = json_decode('{"properties":{"zip":{"type":"number"}}}');
                $data = json_decode('{"zip":"123"}');
                $validator = new JsonSchemaValidator();
                $validator->isValid($data, $schema);

                throw new RequestException($validator);

            case '/error/unexpeced':
                throw new LogicException('This is something unexpected!');
        }
    }

    public static function setUpBeforeClass(): void
    {
        self::cleanup();

        $container = self::createContainer(__DIR__ . '/fixtures/error.neon');
        self::$application = $container->getByType(App::class);
    }

    private function doRequest(string $path): Response
    {
        $request = $this->createRequestGet($path);
        return self::$application->handle($request);
    }

    private function assertErrorStructure(
        array $actual,
        int $expectedCode,
        string $expectedError,
        string $expectedMessage // phpcs:ignore SlevomatCodingStandard.Functions.RequireTrailingCommaInDeclaration
    ): void
    {
        $help = (string) json_encode($actual);
        Assert::assertSame($expectedCode, $actual['code'], $help);
        Assert::assertSame($expectedError, $actual['error'], $help);
        Assert::assertSame($expectedMessage, $actual['message'], $help);
        Assert::assertSame(32, strlen($actual['id']));
    }

    private function assertBaseErrorStrucuture(
        array $actual,
        int $expectedCode,
        string $expectedError,
        string $expectedMessage // phpcs:ignore SlevomatCodingStandard.Functions.RequireTrailingCommaInDeclaration
    ): void
    {
        $help = (string) json_encode($actual);
        Assert::assertSame(['code', 'error', 'message', 'id'], array_keys($actual), $help);
        $this->assertErrorStructure($actual, $expectedCode, $expectedError, $expectedMessage);
    }

    private function assertDetailedErrorStructure(
        array $actual,
        int $expectedCode,
        string $expectedError,
        string $expectedMessage // phpcs:ignore SlevomatCodingStandard.Functions.RequireTrailingCommaInDeclaration
    ): void
    {
        $help = (string) json_encode($actual);
        Assert::assertSame(['code', 'error', 'message', 'id', 'exception'], array_keys($actual), $help);
        $this->assertErrorStructure($actual, $expectedCode, $expectedError, $expectedMessage);
    }

    private function assertExceptionStructure(
        array $actual,
        int $expectedCode,
        string $expectedFile,
        string $expectedMessage,
        string $expectedType,
        string $expectedTrace // phpcs:ignore SlevomatCodingStandard.Functions.RequireTrailingCommaInDeclaration
    ): void
    {
        $help = (string) json_encode($actual);
        Assert::assertSame($expectedCode, $actual['code'], $help);
        Assert::assertSame($expectedFile, $actual['file'], $help);
        Assert::assertIsNumeric($actual['line'], $help);
        Assert::assertSame($expectedMessage, $actual['message'], $help);
        Assert::assertSame($expectedType, $actual['type'], $help);
        Assert::assertIsArray($actual['trace'], $help);
        Assert::assertCount(1, $actual['trace'], $help);
        Assert::assertTrue(strpos($actual['trace'][0], $expectedTrace) !== false, $help);
    }

    private function assertLoggerStructure(array $actual, string $expectedMessage): void
    {
        $help = (string) json_encode($actual);
        Assert::assertArrayHasKey('error', $actual, $help);
        Assert::assertSame($expectedMessage, $actual['error'][0][0], $help);
        Assert::assertArrayHasKey('error', $actual['error'][0][1], $help);
        Assert::assertArrayHasKey('exception', $actual['error'][0][1]['error'], $help);
    }
}
