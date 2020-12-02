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
use SlimAPI\Tests\Functional\TestCase;
use SlimAPI\Validation\Validator\JsonSchemaValidator;

class JsonErrorRendererTest extends TestCase
{
    /** @var App */
    protected static App $application;

    public function testSlimHttpException(): void
    {
        $response = $this->doRequest('/error/slim-http');
        $data = $response->getJson();

        Assert::assertSame(400, $data->code);
        Assert::assertSame('BAD_REQUEST', $data->error);
        Assert::assertSame('Bad request.', $data->message);
        Assert::assertSame(32, strlen($data->id));
    }

    public function testValidationRequestException(): void
    {
        $response = $this->doRequest('/error/validation-request');
        $data = $response->getJson();

        Assert::assertSame(400, $data->code);
        Assert::assertSame('VALIDATION_ERROR', $data->error);
        Assert::assertSame('JSON Schema Validation Error', $data->message);
        Assert::assertSame(32, strlen($data->id));
        Assert::assertEquals(
            [
                (object) [
                    'property' => 'zip',
                    'message' => 'String value found, but a number is required',
                    'constraint' => 'type',
                ],
            ],
            $data->validation,
        );
    }

    public function testSlimApiHttpException(): void
    {
        $response = $this->doRequest('/error/slimapi-http');
        $data = $response->getJson();

        Assert::assertSame(422, $data->code);
        Assert::assertSame('INACTIVE_CLIENT', $data->error);
        Assert::assertSame('Inactive client', $data->message);
        Assert::assertSame(32, strlen($data->id));
    }

    public function testUnexpectedException(): void
    {
        $response = $this->doRequest('/error/unexpeced');
        $data = $response->getJson();

        Assert::assertSame(500, $data->code);
        Assert::assertSame('INTERNAL_SERVER_ERROR', $data->error);
        Assert::assertSame('SlimAPI Application Error', $data->message);
        Assert::assertSame(32, strlen($data->id));
    }

    public function testDetailedErrors(): void
    {
        $container = self::createContainer(__DIR__ . '/fixtures/error_detail.neon');
        $application = $container->getByType(App::class);

        $request = $this->createRequestGet('/error/unexpeced');
        $request = $request->withHeader('Accept', 'application/json');
        $response = $application->handle($request);
        $data = $response->getJson();

        Assert::assertSame(500, $data->code);
        Assert::assertSame('INTERNAL_SERVER_ERROR', $data->error);
        Assert::assertSame('SlimAPI Application Error', $data->message);
        Assert::assertSame(32, strlen($data->id));
        Assert::assertSame(0, $data->exception->code);
        Assert::assertSame('/var/www/tests/Functional/Error/JsonErrorRendererTest.php', $data->exception->file);
        Assert::assertIsNumeric($data->exception->line);
        Assert::assertSame('This is something unexpected!', $data->exception->message);
        Assert::assertSame('LogicException', $data->exception->type);
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
}
