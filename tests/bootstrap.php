<?php

/** phpcs:disable PSR1.Files.SideEffects */

declare(strict_types=1);

use AdrianSuter\Autoload\Override\Override;
use SlimAPI\Http\Message;
use SlimAPI\Tests\TestCase;

$classLoader = require __DIR__ . '/../vendor/autoload.php';

Override::apply($classLoader, [
    Message::class => [
        'preg_split' => static function (string $pattern, string $subject, int $limit = -1, int $flags = 0) {
            if (isset($GLOBALS['preg_split_return'])) {
                return $GLOBALS['preg_split_return'];
            }

            return preg_split($pattern, $subject, $limit, $flags);
        },
    ],
]);

TestCase::cleanup();
