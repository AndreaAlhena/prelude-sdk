<?php

namespace Prelude\SDK\Models;

use DateTime;

/**
 * Transactional Message model
 */
class TransactionalMessage
{
    private string $_callbackUrl;
    private string $_correlationId;
    private DateTime $_createdAt;
    private DateTime $_expiresAt;
    private string $_from;
    private string $_id;
    private array $_rawData;
    private string $_templateId;
    private string $_to;
    private array $_variables;

    /**
     * Create a new TransactionalMessage instance
     * 
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->_id = $data['id'];
        $this->_to = $data['to'];
        $this->_templateId = $data['template_id'];
        $this->_variables = $data['variables'] ?? [];
        $this->_expiresAt = new DateTime($data['expires_at']);
        $this->_createdAt = new DateTime($data['created_at']);
        $this->_from = $data['from'] ?? '';
        $this->_callbackUrl = $data['callback_url'] ?? '';
        $this->_correlationId = $data['correlation_id'] ?? '';
        $this->_rawData = $data;
    }

    /**
     * Get the callback URL
     * 
     * @return string
     */
    public function getCallbackUrl(): string
    {
        return $this->_callbackUrl;
    }

    /**
     * Get the correlation ID
     * 
     * @return string
     */
    public function getCorrelationId(): string
    {
        return $this->_correlationId;
    }

    /**
     * Get the creation date
     * 
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->_createdAt;
    }

    /**
     * Get the expiration date
     * 
     * @return DateTime
     */
    public function getExpiresAt(): DateTime
    {
        return $this->_expiresAt;
    }

    /**
     * Get the sender ID
     * 
     * @return string
     */
    public function getFrom(): string
    {
        return $this->_from;
    }

    /**
     * Get the message ID
     * 
     * @return string
     */
    public function getId(): string
    {
        return $this->_id;
    }

    /**
     * Get the raw response data
     * 
     * @return array
     */
    public function getRawData(): array
    {
        return $this->_rawData;
    }

    /**
     * Get the template ID
     * 
     * @return string
     */
    public function getTemplateId(): string
    {
        return $this->_templateId;
    }

    /**
     * Get the recipient phone number
     * 
     * @return string
     */
    public function getTo(): string
    {
        return $this->_to;
    }

    /**
     * Get the template variables
     * 
     * @return array
     */
    public function getVariables(): array
    {
        return $this->_variables;
    }

    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        $result = [
            'id' => $this->_id,
            'to' => $this->_to,
            'template_id' => $this->_templateId,
            'variables' => $this->_variables,
            'expires_at' => $this->_expiresAt->format('c'),
            'created_at' => $this->_createdAt->format('c'),
        ];

        if (!empty($this->_from)) {
            $result['from'] = $this->_from;
        }

        if (!empty($this->_callbackUrl)) {
            $result['callback_url'] = $this->_callbackUrl;
        }

        if (!empty($this->_correlationId)) {
            $result['correlation_id'] = $this->_correlationId;
        }

        return $result;
    }
}