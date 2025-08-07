<?php

namespace PreludeSo\SDK\ValueObjects\Webhook;

class GenericPayload extends AbstractEventPayload
{
    private array $_data;

    protected function parsePayload(array $payload): void
    {
        $this->_data = $payload;
    }

    /**
     * Get all payload data
     */
    public function getData(): array
    {
        return $this->_data;
    }

    /**
     * Get a specific value from the payload
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->getValue($this->_data, $key, $default);
    }

    /**
     * Check if a key exists in the payload
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->_data);
    }

    public function toArray(): array
    {
        return $this->_data;
    }
}