<?php

declare(strict_types=1);

namespace SlimAPI\Exception\Validation;

use Fig\Http\Message\StatusCodeInterface;
use SlimAPI\Exception\Http\Exception as HttpException;
use SlimAPI\Validation\Validator\ValidatorInterface;

abstract class Exception extends HttpException
{
    protected int $defaultCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;

    protected ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;

        parent::__construct(
            json_encode($validator->getErrors(), JSON_THROW_ON_ERROR),
            $this->defaultCode,
            'VALIDATION_ERROR',
        );
    }

    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }
}
