<?php

declare(strict_types=1);

namespace SlimAPI\Validation;

use SlimAPI\Validation\Generator\DefaultGenerator;
use SlimAPI\Validation\Validator\JsonSchemaValidator;

class Factory
{
    private string $sourceMask;

    private string $outputDir;

    private bool $strict;

    private bool $disableRequestValidation = false;

    private bool $disableResponseValidation = false;

    /**
     * @param string $sourceMask The path where validation schema *.JSON files are stored
     * @param string $outputDir The path where generated PHP cache-file will be stored
     * @param bool $strict The JSON Schema "additionalProperties: false" will be used in every schema
     */
    public function __construct(string $sourceMask, string $outputDir, bool $strict = JsonSchemaValidator::DEFAULT_STRICT)
    {
        $this->sourceMask = $sourceMask;
        $this->outputDir = $outputDir;
        $this->strict = $strict;
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

    public function createGenerator(): DefaultGenerator
    {
        return new DefaultGenerator($this->sourceMask, $this->outputDir);
    }

    public function createConfigurator(): Configurator
    {
        $configurator = new Configurator($this->createGenerator(), new JsonSchemaValidator($this->strict));
        $configurator->disableRequestValidation($this->disableRequestValidation);
        $configurator->disableResponseValidation($this->disableResponseValidation);

        return $configurator;
    }
}
