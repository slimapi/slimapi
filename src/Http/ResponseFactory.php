<?php

declare(strict_types=1);

namespace SlimAPI\Http;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Headers;

class ResponseFactory extends \Slim\Psr7\Factory\ResponseFactory
{
    public function createResponse(int $code = StatusCodeInterface::STATUS_OK, string $reasonPhrase = ''): ResponseInterface
    {
        $old = parent::createResponse($code, $reasonPhrase);

        return new Response(
            $old->getStatusCode(),
            new Headers($old->getHeaders()),
            $old->getBody(),
        );
    }
}
