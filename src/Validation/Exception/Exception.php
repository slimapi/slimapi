<?php

declare(strict_types=1);

namespace SlimAPI\Validation\Exception;

use Fig\Http\Message\StatusCodeInterface;
use SlimAPI\Exception\Http\Exception as HttpException;
use SlimAPI\Validation\Validator\ValidatorInterface;

abstract class Exception extends HttpException
{
    protected int $defaultCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;

    public function __construct(ValidatorInterface $validator)
    {
        parent::__construct($validator->generateErrorMessage(), $this->defaultCode, 'VALIDATION_ERROR');
    }
}
