<?php

namespace Prelude\SDK\Services;

use Prelude\SDK\Config\Config;
use Prelude\SDK\Exceptions\PreludeException;
use Prelude\SDK\Http\HttpClient;
use Prelude\SDK\Models\TransactionalMessage;
use Prelude\SDK\ValueObjects\Transactional\TransactionalOptions;

/**
 * Transactional Service for sending transactional messages
 */
class TransactionalService
{
    private HttpClient $_httpClient;

    /**
     * Create a new Transactional service instance
     * 
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->_httpClient = $httpClient;
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
    public function send(string $to, string $templateId, ?TransactionalOptions $options = null): TransactionalMessage
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