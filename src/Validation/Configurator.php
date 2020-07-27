<?php

declare(strict_types=1);

namespace SlimAPI\Validation;

use SlimAPI\App;
use SlimAPI\Configurator\ConfiguratorInterface;
use SlimAPI\Validation\Middleware\RequestMiddleware;
use SlimAPI\Validation\Middleware\ResponseMiddleware;
use SlimAPI\Validation\Validator\ValidatorInterface;

class Configurator implements ConfiguratorInterface
{
    private Generator $generator;

    private ValidatorInterface $validator;

    public function __construct(Generator $generator, ValidatorInterface $validator)
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
