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

    private bool $disableRequestValidation = false;

    private bool $disableResponseValidation = false;

    public function __construct(Generator $generator, Validator $validator)
    {
        $this->generator = $generator;
        $this->validator = $validator;
    }

    public function disableRequestValidation(bool $disable): self
    {
        $this->disableRequestValidation = $disable;
        return $this;
    }

    public function disableResponseValidation(bool $disable): self
    {
        $this->disableResponseValidation = $disable;
        return $this;
    }

    public function configureApplication(App $application): void
    {
        if ($this->disableResponseValidation === false) {
            $application->add(new ResponseMiddleware($this->generator, $this->validator));
        }

        if ($this->disableRequestValidation === false) {
            $application->add(new RequestMiddleware($this->generator, $this->validator));
        }
    }
}
