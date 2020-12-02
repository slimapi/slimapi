<?php

declare(strict_types=1);

namespace SlimAPI\Testing;

use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Headers;
use SlimAPI\Http\Request;

trait RequestHelper
{
    protected array $onCreatedRequest = [];

    protected function createRequestGet(string $path, array $query = [], array $headers = []): Request
    {
        return $this->createRequest('GET', $path, $query, [], $headers);
    }

    protected function createRequestPost(string $path, array $data, array $query = [], array $headers = []): Request
    {
        return $this->createRequest('POST', $path, $query, $data, $headers);
    }

    protected function createRequestPut(string $path, array $data, array $query = [], array $headers = []): Request
    {
        return $this->createRequest('PUT', $path, $query, $data, $headers);
    }

    protected function createRequestDelete(string $path, array $query = [], array $headers = []): Request
    {
        return $this->createRequest('DELETE', $path, $query, [], $headers);
    }

    protected function createRequest(string $method, string $path, array $query = [], array $data = [], array $headers = []): Request // phpcs:ignore Generic.Files.LineLength
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
        $request = $request->withHeader('Accept', 'application/json');

        foreach ($this->onCreatedRequest as $callback) {
            if (is_callable($callback)) {
                $request = $callback($request);
            }
        }

        return $request;
    }
}
