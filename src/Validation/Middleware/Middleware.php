<?php

declare(strict_types=1);

namespace SlimAPI\Validation\Middleware;

use SlimAPI\Validation\Generator;
use SlimAPI\Validation\Validator\ValidatorInterface;

abstract class Middleware
{
    protected Generator $generator;

    protected ValidatorInterface $validator;

    public function __construct(Generator $generator, ValidatorInterface $validator)
    {
        $this->generator = $generator;
        $this->validator = $validator;
    }
}
