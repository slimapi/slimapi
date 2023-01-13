<?php

declare(strict_types=1);

namespace SlimAPI\Http;

use Fig\Http\Message\StatusCodeInterface;

/**
 * @method Response withHeader(string $name, mixed $value);
 * @method Response withStatus(int $code, string $reasonPhrase = '');
 */
class Response extends \Slim\Psr7\Response
{
    use Message;

    public const ATTRIBUTE_SKIP_VALIDATION = '__skipValidation__';

    protected array $attributes;

    /**
     * Write data to the response body.
     */
    public function write(string $data): self
    {
        $this->getBody()->write($data);
        return $this;
    }

    /**
     * Return an instance with the data encoded as JSON with suitable "Content-Type" header.
     */
    public function withJson(array $data, int $status = StatusCodeInterface::STATUS_OK, int $options = JSON_THROW_ON_ERROR): self
    {
        $clone = clone $this;

        $payload = json_encode($data, $options);
        $clone->getBody()->write($payload); // @phpstan-ignore-line

        return $clone
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }

    /**
     * Return an instance with status code 204.
     */
    public function withNoContent(): self
    {
        return (clone $this)->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);
    }

    /**
     * Return an instance with the specified derived request attribute.
     */
    public function withAttribute(string $name, mixed $value): self
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;

        return $clone;
    }

    /**
     * Skip validation for this response.
     */
    public function skipValidation(): self
    {
        return (clone $this)->withAttribute(self::ATTRIBUTE_SKIP_VALIDATION, true);
    }

    /**
     * Retrieve a single derived request attribute.
     */
    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * Is this response OK?
     */
    public function isOk(): bool
    {
        return $this->getStatusCode() === StatusCodeInterface::STATUS_OK;
    }

    /**
     * Is this response successful?
     */
    public function isSuccessful(): bool
    {
        return $this->getStatusCode() >= StatusCodeInterface::STATUS_OK &&
            $this->getStatusCode() < StatusCodeInterface::STATUS_MULTIPLE_CHOICES;
    }
}
