<?php

/** phpcs:disable PSR1.Files.SideEffects */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use SlimAPI\Tests\TestCase;

define('__TMP_DIR__', __DIR__ . '/../tmp');
define('__FIXTURES_DIR__', __DIR__ . '/Functional/_fixtures');

TestCase::cleanup();
