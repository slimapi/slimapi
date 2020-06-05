<?php

declare(strict_types=1);

namespace SlimAPI\Http;

trait Message
{
    /**
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     * @return mixed|null
     */
    public function getJson(bool $assoc = true, int $depth = 512, int $options = JSON_THROW_ON_ERROR)
    {
        if ($this->body === null || $this->body->getSize() === 0) {
            return null;
        }

        return json_decode((string) $this->body, $assoc, $depth, $options);
    }
}
