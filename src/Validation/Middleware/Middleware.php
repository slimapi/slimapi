<?php

declare(strict_types=1);

namespace SlimAPI\Validation\Middleware;

use SlimAPI\Validation\Generator\GeneratorInterface as Generator;
use SlimAPI\Validation\Validator\ValidatorInterface as Validator;

abstract class Middleware
{
    protected Generator $generator;

    protected Validator $validator;

    public function __construct(Generator $generator, Validator $validator)
    {
        $this->generator = $generator;
        $this->validator = $validator;
    }
}
