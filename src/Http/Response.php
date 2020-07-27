<?php

declare(strict_types=1);

namespace SlimAPI\Http;

use Fig\Http\Message\StatusCodeInterface;
use Slim\Psr7\Response as BaseResponse;

/**
 * @method Response withHeader(string $name, mixed $value);
 * @method Response withStatus(int $code, string $reasonPhrase = '');
 */
class Response extends BaseResponse
{
    use Message;

    public const ATTRIBUTE_SKIP_VALIDATION = '__skipValidation__';

    protected array $attributes;

    /**
     * Write data to the response body.
     * @param string $data
     * @return $this
     */
    public function write(string $data): self
    {
        $this->getBody()->write($data);
        return $this;
    }

    /**
     * Return an instance with the data encoded as JSON with suitable "Content-Type" header.
     * @param array $data
     * @param int $status
     * @param int $options
     * @return self
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
     * @return self
     */
    public function withNoContent(): self
    {
        $clone = clone $this;
        return $clone->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);
    }

    /**
     * Return an instance with the specified derived request attribute.
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function withAttribute(string $name, $value): self
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;

        return $clone;
    }

    /**
     * Skip validation for this response.
     * @return self
     */
    public function skipValidation(): self
    {
        $clone = clone $this;
        return $clone->withAttribute(self::ATTRIBUTE_SKIP_VALIDATION, true);
    }

    /**
     * Retrieve a single derived request attribute.
     * @param string $name
     * @param mixed $default
     * @return mixed|null
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * Is this response OK?
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->getStatusCode() === StatusCodeInterface::STATUS_OK;
    }

    /**
     * Is this response successful?
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->getStatusCode() >= StatusCodeInterface::STATUS_OK &&
            $this->getStatusCode() < StatusCodeInterface::STATUS_MULTIPLE_CHOICES;
    }
}
