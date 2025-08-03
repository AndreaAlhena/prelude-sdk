<?php

namespace PreludeSo\SDK\Exceptions;

/**
 * Exception thrown when API requests fail
 */
class ApiException extends PreludeException
{
    private ?array $_responseData;
    
    /**
     * Create a new API exception
     * 
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     * @param array|null $responseData
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        \Exception $previous = null,
        array $responseData = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->_responseData = $responseData;
    }
    
    /**
     * Get the response data from the failed API request
     * 
     * @return array|null
     */
    public function getResponseData(): ?array
    {
        return $this->_responseData;
    }
    
    /**
     * Check if this is a client error (4xx)
     * 
     * @return bool
     */
    public function isClientError(): bool
    {
        return $this->getCode() >= 400 && $this->getCode() < 500;
    }
    
    /**
     * Check if this is a server error (5xx)
     * 
     * @return bool
     */
    public function isServerError(): bool
    {
        return $this->getCode() >= 500 && $this->getCode() < 600;
    }
}