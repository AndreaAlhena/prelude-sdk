<?php

namespace PreludeSo\SDK\ValueObjects\Webhook;

abstract class AbstractEventPayload
{
    protected array $_rawPayload;

    public function __construct(array $payload)
    {
        $this->_rawPayload = $payload;
        $this->validatePayload($payload);
        $this->parsePayload($payload);
    }

    /**
     * Get the raw payload data
     */
    public function getRawPayload(): array
    {
        return $this->_rawPayload;
    }

    /**
     * Convert the payload to an array representation
     */
    abstract public function toArray(): array;

    /**
     * Validate the payload structure
     */
    protected function validatePayload(array $payload): void
    {
        // Default implementation - can be overridden by subclasses
    }

    /**
     * Parse the payload and set properties
     */
    abstract protected function parsePayload(array $payload): void;

    /**
     * Get a value from the payload with optional default
     */
    protected function getValue(array $payload, string $key, mixed $default = null): mixed
    {
        return $payload[$key] ?? $default;
    }

    /**
     * Get a nested value from the payload
     */
    protected function getNestedValue(array $payload, array $keys, mixed $default = null): mixed
    {
        $value = $payload;
        foreach ($keys as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return $default;
            }
            $value = $value[$key];
        }
        return $value;
    }
}
