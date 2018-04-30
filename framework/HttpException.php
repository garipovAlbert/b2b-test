<?php

namespace testframework;

use Exception;
use Throwable;

/**
 * @author albert
 */
class HttpException extends Exception implements DataObject
{

    public $statusCode;

    public function __construct(int $statusCode, string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->statusCode = $statusCode;
        parent::__construct($message, $code, $previous);
    }

    public function getData(): array
    {
        return [
            'status' => $this->statusCode,
            'error' => $this->message,
        ];
    }

}