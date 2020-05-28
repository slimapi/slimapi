<?php

declare(strict_types=1);

namespace SlimAPI\DI;

use Nette\DI\Config\Adapter;
use Nette\DI\Config\Adapters\NeonAdapter as BaseNeonAdapter;
use Nette\Neon\Neon;

/**
 * Improved NeonAdapter with environment variables substitution.
 * CAUTION: You have to installed GNU envsubst!
 * @link https://www.gnu.org/software/gettext/manual/html_node/envsubst-Invocation.html
 */
class NeonAdapter implements Adapter
{
    private BaseNeonAdapter $adapter;

    public function __construct()
    {
        $this->adapter = new BaseNeonAdapter();
    }

    public function dump(array $data): string
    {
        return $this->adapter->dump($data);
    }

    public function load(string $file): array
    {
        return $this->adapter->process((array) Neon::decode((string) shell_exec("envsubst < $file")));
    }
}
