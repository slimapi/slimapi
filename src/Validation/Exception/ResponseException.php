<?php

declare(strict_types=1);

namespace SlimAPI\Validation\Exception;

use Slim\Exception\HttpSpecializedException;
use SlimAPI\Http\Request;
use SlimAPI\Validation\Validator\ValidatorInterface;

class ResponseException extends HttpSpecializedException
{
    public function __construct(Request $request, ValidatorInterface $validator)
    {
        parent::__construct($request, $validator->generateErrorMessage());
    }
}
