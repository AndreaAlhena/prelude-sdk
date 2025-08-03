<?php

namespace PreludeSo\SDK\Services;

use PreludeSo\SDK\Config\Config;
use PreludeSo\SDK\Exceptions\PreludeException;
use PreludeSo\SDK\Http\HttpClient;
use PreludeSo\SDK\Models\TransactionalMessage;
use PreludeSo\SDK\ValueObjects\Transactional\Options;

/**
 * Transactional Service for sending transactional messages
 */
final class TransactionalService
{
    /**
     * Create a new Transactional service instance
     * 
     * @param HttpClient $httpClient
     */
    public function __construct(private HttpClient $_httpClient)
    {
        //
    }

    /**
     * Send a transactional message
     * 
     * @param string $to The recipient's phone number (E.164 format)
     * @param string $templateId The template identifier
     * @param TransactionalOptions|null $options Additional options
     * @return TransactionalMessage
     * @throws PreludeException
     */
    public function send(string $to, string $templateId, ?Options $options = null): TransactionalMessage
    {
        $data = [
            'to' => $to,
            'template_id' => $templateId
        ];

        if ($options) {
            $data = array_merge($data, $options->toArray());
        }

        $response = $this->_httpClient->post(Config::ENDPOINT_TRANSACTIONAL, $data);

        return new TransactionalMessage($response);
    }
}