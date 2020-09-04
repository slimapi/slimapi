<?php

declare(strict_types=1);

namespace SlimAPI\Validation;

use SlimAPI\App;
use SlimAPI\Configurator\ConfiguratorInterface;
use SlimAPI\Validation\Generator\GeneratorInterface as Generator;
use SlimAPI\Validation\Middleware\RequestMiddleware;
use SlimAPI\Validation\Middleware\ResponseMiddleware;
use SlimAPI\Validation\Validator\ValidatorInterface as Validator;

class Configurator implements ConfiguratorInterface
{
    private Generator $generator;

    private Validator $validator;

    public function __construct(Generator $generator, Validator $validator)
    {
        $this->generator = $generator;
        $this->validator = $validator;
    }

    public function configureApplication(App $application): void
    {
        $application->add(new ResponseMiddleware($this->generator, $this->validator));
        $application->add(new RequestMiddleware($this->generator, $this->validator));
    }
}
