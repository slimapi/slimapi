<?php

declare(strict_types=1);

namespace SlimAPI\Testing;

use InvalidArgumentException;
use JsonSerializable;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Headers;
use SlimAPI\Http\Request;
use stdClass;

trait RequestHelper
{
    protected array $onCreatedRequest = [];

    protected function createRequestGet(string $path, array $query = [], array $headers = []): Request
    {
        return $this->createRequest('GET', $path, $query, null, $headers);
    }

    /**
     * @param string $path
     * @param array|stdClass|JsonSerializable $data
     * @param array $query
     * @param array $headers
     * @return Request
     */
    protected function createRequestPost(string $path, $data, array $query = [], array $headers = []): Request
    {
        return $this->createRequest('POST', $path, $query, $data, $headers);
    }

    /**
     * @param string $path
     * @param array|stdClass|JsonSerializable $data
     * @param array $query
     * @param array $headers
     * @return Request
     */
    protected function createRequestPut(string $path, $data, array $query = [], array $headers = []): Request
    {
        return $this->createRequest('PUT', $path, $query, $data, $headers);
    }

    protected function createRequestDelete(string $path, array $query = [], array $headers = []): Request
    {
        return $this->createRequest('DELETE', $path, $query, null, $headers);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $query
     * @param array|stdClass|JsonSerializable $data
     * @param array $headers
     * @return Request
     */
    protected function createRequest(string $method, string $path, array $query = [], $data = null, array $headers = []): Request
    {
        $headers = new Headers($headers);
        $body = (new StreamFactory())->createStream();

        if ($data !== null) {
            if ($data instanceof stdClass) {
                $data = (array) $data;
            }

            if (is_array($data) === false && !$data instanceof JsonSerializable) {
                throw new InvalidArgumentException(sprintf(
                    'Argument "data" can be type of array|stdclass|JsonSerializable, "%s" given.',
                    get_class($data),
                ));
            }

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
