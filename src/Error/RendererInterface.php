<?php

declare(strict_types=1);

namespace SlimAPI\Error;

use Throwable;

interface RendererInterface
{
    public function __invoke(Throwable $exception, bool $displayErrorDetails, bool $displayAsString): mixed;
}
