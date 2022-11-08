<?php

declare(strict_types=1);

namespace SlimAPI\Error;

use Throwable;

interface RendererInterface
{
    /**
     * @param Throwable $exception
     * @param bool $displayErrorDetails
     * @param bool $displayAsString
     * @return mixed
     */
    public function __invoke(Throwable $exception, bool $displayErrorDetails, bool $displayAsString);
}
