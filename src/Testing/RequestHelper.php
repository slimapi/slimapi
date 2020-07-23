<?php

declare(strict_types=1);

namespace SlimAPI\Testing;

use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Headers;
use SlimAPI\Http\Request;

trait RequestHelper
{
    protected static function createRequestGet(string $path, array $query = [], array $headers = []): Request
    {
        return self::createRequest('GET', $path, $query, [], $headers);
    }

    protected static function createRequestPost(string $path, array $data, array $query = [], array $headers = []): Request
    {
        return self::createRequest('POST', $path, $query, $data, $headers);
    }

    protected static function createRequestPut(string $path, array $data, array $query = [], array $headers = []): Request
    {
        return self::createRequest('PUT', $path, $query, $data, $headers);
    }

    protected static function createRequestDelete(string $path, array $query = [], array $headers = []): Request
    {
        return self::createRequest('DELETE', $path, $query, [], $headers);
    }

    protected static function createRequest(string $method, string $path, array $query = [], array $data = [], array $headers = []): Request // phpcs:ignore Generic.Files.LineLength
    {
        $headers = new Headers($headers);

        $body = (new StreamFactory())->createStream();
        if ($data !== []) {
            $body->write(json_encode($data, JSON_THROW_ON_ERROR));
            $headers->addHeader('Content-Type', 'application/json');
        }

        $request = new Request(
            $method,
            (new UriFactory())->createUri($path),
            $headers,
            [],
            [],
            $body,
        );

        /** @var Request $request */
        $request = $request->withQueryParams($query);
        return $request;
    }
}
