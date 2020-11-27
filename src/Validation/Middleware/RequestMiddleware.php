<?php

declare(strict_types=1);

namespace SlimAPI\Validation\Middleware;

use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SlimAPI\Exception\Http\BadRequestException;
use SlimAPI\Exception\LogicException;
use SlimAPI\Exception\Validation\RequestException;
use SlimAPI\Http\Request;
use SlimAPI\Validation\Mapper;
use SlimAPI\Validation\Validator\ValidatorInterface;

class RequestMiddleware extends Middleware
{
    protected function getSchema(Mapper $mapper, Request $request): array
    {
        /** @var string $schemaName */
        $schemaName = $mapper->getValue() ?? $this->requestToSchemaName($request);
        $schemaList = $this->generator->getSchemaList();
        if (!isset($schemaList[$schemaName])) {

            // Try API Blueprint Optional Parameter Format
            // FastRoute Optional Parameter:        /path/[{id}]
            // API Blueprint Optional Parameter:    /path/{?id}
            $schemaName = str_replace(['[{', '}]'], ['{?', '}'], $schemaName);
            if (isset($schemaList[$schemaName])) {
                return $schemaList[$schemaName];
            }

            throw new LogicException(sprintf(
                'Validation schema for request [%s %s] has not been found.',
                $request->getMethod(),
                $request->getRoute()->getPattern(),
            ));
        }

        return $schemaList[$schemaName];
    }

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

        $schema = $this->getSchema($mapper, $request);
        $request = $request->withAttribute(Request::ATTRIBUTE_VALIDATION_SCHEMA, $schema);
        if ($schema[0]->meta->type !== ValidatorInterface::TYPE_REQUEST) { // skip validation of request without required body
            return $handler->handle($request);
        }

        if ((string) $request->getBody() === '') {
            throw new BadRequestException('Missing request body.');
        }

        if ($request->getMediaType() !== ValidatorInterface::CONTENT_TYPE) {
            throw new BadRequestException(sprintf("Supported content-type is '%s' only.", ValidatorInterface::CONTENT_TYPE));
        }

        try {
            $body = $request->getJson(false);
        } catch (JsonException $e) {
            throw new BadRequestException('Bad request body.', $e);
        }

        if ($this->validator->isValid($body, $schema[0]->schema) === false) {
            throw new RequestException($this->validator);
        }

        return $handler->handle($request);
    }
}
