<?php

declare(strict_types=1);

namespace SlimAPI\Http;

use SlimAPI\Exception\Http\BadRequestException;
use SlimAPI\Exception\Http\InternalServerError;
use function preg_split;

trait Message
{
    /**
     * Return decoded message body.
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     * @return mixed
     */
    public function getJson(bool $assoc = false, int $depth = 512, int $options = JSON_THROW_ON_ERROR)
    {
        $body = (string) $this->body;
        if ($body === '') {
            $exception = self::class === Request::class
                ? BadRequestException::class
                : InternalServerError::class;

            throw new $exception('Empty body cannot be parsed.');
        }

        return json_decode($body, $assoc, $depth, $options);
    }

    /**
     * Get content type from header "Content-Type" if known.
     * @link https://github.com/slimphp/Slim-Http/blob/6115de/src/ServerRequest.php#L460-L464
     * @return string|null
     */
    public function getContentType(): ?string
    {
        $result = $this->getHeader('Content-Type');
        return $result[0] ?? null;
    }

    /**
     * Get media type from header "Content-Type" minus content-type params if known.
     * @link https://github.com/slimphp/Slim-Http/blob/6115de/src/ServerRequest.php#L508-L521
     * @return string|null
     */
    public function getMediaType(): ?string
    {
        $contentType = $this->getContentType();

        if ($contentType !== null) {
            $contentTypeParts = preg_split('/\s*[;,]\s*/', $contentType);
            if ($contentTypeParts === false) {
                return null;
            }

            return strtolower($contentTypeParts[0]);
        }

        return null;
    }
}
