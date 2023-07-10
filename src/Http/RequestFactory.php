<?php

declare(strict_types=1);

namespace SlimAPI\Http;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Headers;

class RequestFactory extends ServerRequestFactory
{
    /**
     * {@inheritdoc}
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        $old = parent::createServerRequest($method, $uri, $serverParams);

        return new Request(
            $old->getMethod(),
            $old->getUri(),
            new Headers($old->getHeaders()),
            $old->getCookieParams(),
            $old->getServerParams(),
            $old->getBody(),
            $old->getUploadedFiles(),
        );
    }

    public static function createFromGlobals(): Request
    {
        $old = parent::createFromGlobals();

        return new Request(
            $old->getMethod(),
            $old->getUri(),
            new Headers($old->getHeaders()),
            $old->getCookieParams(),
            $old->getServerParams(),
            $old->getBody(),
            $old->getUploadedFiles(),
        );
    }
}
