<?php

declare(strict_types=1);

namespace SlimAPI\Http;

use Slim\Psr7\Request as BaseRequest;

class Request extends BaseRequest
{
    use Message;
}
