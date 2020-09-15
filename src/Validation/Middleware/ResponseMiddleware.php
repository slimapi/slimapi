<?php

declare(strict_types=1);

namespace SlimAPI\Validation\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SlimAPI\Http\Request;
use SlimAPI\Http\Response;
use SlimAPI\Validation\Exception\ResponseException;
use SlimAPI\Validation\Validator\ValidatorInterface as Validator;

class ResponseMiddleware extends Middleware
{
    public function __invoke(Request $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Response $response */
        $response = $handler->handle($request);

        // validates only response with status code 2xx
        if (!$response->isSuccessful()) {
            return $response;
        }

        // possibility to skip validation
        if ($response->getAttribute(Response::ATTRIBUTE_SKIP_VALIDATION, false) !== false) {
            return $response;
        }

        if ($response->getMediaType() === Validator::CONTENT_TYPE) {
            $schema = $request->getValidationSchema();
            foreach ($schema as $item) {
                if ($item->meta->type !== Validator::TYPE_RESPONSE) {
                    continue;
                }

                if ($this->validator->isValid($response->getJson(false), $item->schema) === false) {
                    throw new ResponseException($this->validator);
                }

                break; // validates only by first response validation schema
            }
        }

        return $response;
    }
}
