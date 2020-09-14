<?php

/** phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint */

declare(strict_types=1);

namespace SlimAPI\Exception\Http;

use Slim\Exception\HttpSpecializedException;

class UnprocessableEntityException extends HttpSpecializedException
{
    protected $code = 422;
    protected $message = 'Unprocessable entity.';
    protected $title = '422 Unprocessable Entity';
    protected $description = 'The server understands the request, but it was unable to process the contained instructions.';
}
