<?php

declare(strict_types=1);

namespace SlimAPI\Validation\Middleware;

use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpBadRequestException;
use SlimAPI\Exception\LogicException;
use SlimAPI\Http\Request;
use SlimAPI\Validation\Exception\RequestException;
use SlimAPI\Validation\Validator\ValidatorInterface;

class RequestMiddleware extends Middleware
{
    protected function requestToSchemaName(Request $request): string
    {
        return sprintf('[%s]%s', $request->getMethod(), $request->getRoute()->getPattern());
    }

    public function __invoke(Request $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $mapper = $request->getRoute()->getSettings()->getValidationMapper();
        if ($mapper->skipValidation()) {
            return $handler->handle($request);
        }

        $schemaList = $this->generator->getSchemaList();
        $schemaName = $mapper->getValue() ?? $this->requestToSchemaName($request);
        if (!isset($schemaList[$schemaName])) {
            throw new LogicException(sprintf(
                'Validation schema for request [%s %s] has not been found.',
                $request->getMethod(),
                $request->getRoute()->getPattern(),
            ));
        }

        $schema = $schemaList[$schemaName];
        $request = $request->withAttribute(Request::ATTRIBUTE_VALIDATION_SCHEMA, $schema);
        if ($schema[0]->meta->type !== ValidatorInterface::TYPE_REQUEST) { // skip validation of request without required body
            return $handler->handle($request);
        }

        if ((string) $request->getBody() === '') {
            throw new HttpBadRequestException($request, 'Missing request body.');
        }

        if ($request->getMediaType() !== ValidatorInterface::CONTENT_TYPE) {
            throw new HttpBadRequestException(
                $request,
                sprintf("Supported content-type is '%s' only.", ValidatorInterface::CONTENT_TYPE),
            );
        }

        try {
            $body = $request->getJson(false);
        } catch (JsonException $e) {
            throw new HttpBadRequestException($request, 'Bad request body.', $e);
        }

        if ($this->validator->isValid($body, $schema[0]->schema) === false) {
            throw new RequestException($request, $this->validator);
        }

        return $handler->handle($request);
    }
}
