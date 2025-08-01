<?php

namespace Prelude\SDK\Exceptions;

use Exception;

/**
 * Base exception class for all Prelude SDK exceptions
 */
class PreludeException extends Exception
{
    /**
     * Create a new Prelude exception
     * 
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $message = '', int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}