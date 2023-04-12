<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class AuthException extends Exception
{
    protected $message;
    protected $code;

    /**
     * InvalidResponseFormatException constructor.
     * @param string $message
     * @param int|null $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string    $message,
        ?int       $code = null,
        Throwable $previous = null
    ) {
        $this->message = $message;
        $this->code = $code;
        parent::__construct($this->message, $this->code, $previous);
    }
}
