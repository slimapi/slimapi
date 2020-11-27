<?php

declare(strict_types=1);

namespace SlimAPI\Error;

use Fig\Http\Message\StatusCodeInterface;
use Slim\Error\AbstractErrorRenderer;
use Slim\Exception\HttpException;
use SlimAPI\Exception\Http\Exception;
use SlimAPI\Exception\Http\Generator;
use SlimAPI\Exception\Validation\RequestException;
use Throwable;

class JsonErrorRenderer extends AbstractErrorRenderer
{
    use Generator;

    protected function generateError(Throwable $exception): array
    {
        if ($exception instanceof HttpException) {
            $code = $exception->getCode();
            $error = [
                'code' => $code,
                'error' => $this->getErrorFromCode($code),
                'message' => $exception->getMessage(),
            ];

        } elseif ($exception instanceof RequestException) {
            $error = [
                'code' => $exception->getCode(),
                'error' => $exception->getError(),
                'validation' => $exception->getValidator()->getErrors(),
                'message' => $exception->getValidator()->getErrorMessage(),
            ];

        } elseif ($exception instanceof Exception) {
            $error = [
                'code' => $exception->getCode(),
                'error' => $exception->getError(),
                'message' => $exception->getMessage(),
            ];

        } else {
            $error = [
                'code' => StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                'error' => 'INTERNAL_SERVER_ERROR',
                'message' => 'SlimAPI Application Error',
            ];
        }

        $error['id'] = spl_object_hash($exception);
        return $error;
    }

    public function __invoke(Throwable $exception, bool $displayErrorDetails): string
    {
        $error = $this->generateError($exception);

        if ($displayErrorDetails) {
            $error['exception'] = [
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage(),
                'type' => get_class($exception),
            ];
        }

        return (string) json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
