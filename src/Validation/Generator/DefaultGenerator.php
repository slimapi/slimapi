<?php

declare(strict_types=1);

namespace SlimAPI\Validation\Generator;

use SlimAPI\Exception\InvalidArgumentException;

class DefaultGenerator implements GeneratorInterface
{
    private string $sourceMask;

    private string $cacheDir;

    /** @var array|false */
    private $schemaList;

    /**
     * @param string $sourceMask The path where validation schema *.JSON files are stored
     * @param string $cacheDir The path where generated PHP cache-file will be stored
     */
    public function __construct(string $sourceMask, string $cacheDir)
    {
        $this->sourceMask = $sourceMask;
        $this->cacheDir = $cacheDir;
    }

    public function generateSchemaList(): array
    {
        $list = [];
        /** @var string[] $schemas */
        $schemas = (array) glob($this->sourceMask);
        if ($schemas === []) {
            throw new InvalidArgumentException("Validation schema has not been found. Used mask: '$this->sourceMask'.");
        }

        foreach ($schemas as $filename) {
            if (is_file($filename) === false) {
                throw new InvalidArgumentException(sprintf(
                    "Cannot read file '%s'. Please check %%sourceMask%% parameter for %s.",
                    $this->sourceMask,
                    self::class,
                ));
            }

            $json = file_get_contents($filename);
            $list = array_merge($list, (array) json_decode($json, false, JSON_THROW_ON_ERROR)); // @phpstan-ignore-line
        }

        $data = var_export($list, true);
        $data = str_replace('stdClass::__set_state', '(object)', $data);

        $ok = @file_put_contents($this->getCacheFileName(), "<?php return $data;"); // phpcs:ignore Generic.PHP.NoSilencedErrors
        if ($ok === false) {
            throw new InvalidArgumentException(sprintf(
                "Failed to create validation cache-file '%s'. Please check %%cacheDir%% parameter for %s.",
                $this->getCacheFileName(),
                self::class,
            ));
        }

        return $list;
    }

    public function getSchemaList(): array
    {
        if ($this->schemaList === null) {
            $this->schemaList = @include $this->getCacheFileName(); // phpcs:ignore Generic.PHP.NoSilencedErrors
            if ($this->schemaList === false) {
                $this->schemaList = $this->generateSchemaList();
            }
        }

        return $this->schemaList;
    }

    public function getCacheFileName(): string
    {
        return $this->cacheDir . '/validation.php';
    }
}
