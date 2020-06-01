<?php

declare(strict_types=1);

namespace SlimAPI\Http;

use Fig\Http\Message\StatusCodeInterface;
use Slim\Psr7\Response as BaseResponse;

class Response extends BaseResponse
{
    public function write(string $data): self
    {
        $this->getBody()->write($data);
        return $this;
    }

    public function withJson(array $data, int $status = StatusCodeInterface::STATUS_OK, int $options = JSON_THROW_ON_ERROR): self
    {
        $clone = clone $this;

        $payload = json_encode($data, $options);
        $clone->getBody()->write($payload);

        return $clone
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }

    public function withNoContent(): self
    {
        $clone = clone $this;
        return $clone->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);
    }
}
