<?php

namespace PreludeSo\SDK\ValueObjects\Transactional;

/**
 * Transactional Options value object
 */
class Options
{
    private string $_callbackUrl;
    private string $_correlationId;
    private string $_expiresAt;
    private string $_from;
    private string $_locale;
    private array $_variables;

    /**
     * Create a new TransactionalOptions instance
     * 
     * @param array $variables Template variables
     * @param string $from Sender ID
     * @param string $locale BCP-47 formatted locale string
     * @param string $expiresAt Message expiration date
     * @param string $callbackUrl Callback URL
     * @param string $correlationId User-defined identifier
     */
    public function __construct(
        array $variables = [],
        string $from = '',
        string $locale = '',
        string $expiresAt = '',
        string $callbackUrl = '',
        string $correlationId = ''
    ) {
        $this->_variables = $variables;
        $this->_from = $from;
        $this->_locale = $locale;
        $this->_expiresAt = $expiresAt;
        $this->_callbackUrl = $callbackUrl;
        $this->_correlationId = $correlationId;
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
     * Get the expiration date
     * 
     * @return string
     */
    public function getExpiresAt(): string
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
     * Get the locale
     * 
     * @return string
     */
    public function getLocale(): string
    {
        return $this->_locale;
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
     * Convert to array for API request
     * 
     * @return array
     */
    public function toArray(): array
    {
        $result = [];

        if (!empty($this->_variables)) {
            $result['variables'] = $this->_variables;
        }

        if (!empty($this->_from)) {
            $result['from'] = $this->_from;
        }

        if (!empty($this->_locale)) {
            $result['locale'] = $this->_locale;
        }

        if (!empty($this->_expiresAt)) {
            $result['expires_at'] = $this->_expiresAt;
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