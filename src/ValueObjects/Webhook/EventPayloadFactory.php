<?php

namespace PreludeSo\SDK\ValueObjects\Webhook;

use PreludeSo\SDK\Enums\WebhookEventType;

class EventPayloadFactory
{
    private const PAYLOAD_TYPE_MAP = [
        'verify.' => VerifyPayload::class,
        'transactional.' => TransactionalPayload::class,
    ];

    /**
     * Create a payload object based on the event type
     */
    public static function create(array $payload, string $type): AbstractEventPayload
    {
        if (empty($type)) {
            throw new \InvalidArgumentException('Event type cannot be empty');
        }

        $payloadClass = self::_getPayloadClass($type);
        
        try {
            return new $payloadClass($payload);
        } catch (\Throwable $e) {
            throw new \RuntimeException(
                sprintf('Failed to create payload for type "%s": %s', $type, $e->getMessage()),
                0,
                $e
            );
        }
    }

    /**
     * Check if a payload type is supported
     */
    public static function isSupported(string $type): bool
    {
        // Check if it's a known enum case
        if (self::_isKnownEventType($type)) {
            return true;
        }
        
        // Check if it matches a supported prefix
        foreach (array_keys(self::PAYLOAD_TYPE_MAP) as $prefix) {
            if (str_starts_with($type, $prefix)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check if the event type is a known WebhookEventType enum case
     */
    private static function _isKnownEventType(string $type): bool
    {
        return in_array($type, array_map(fn($case) => $case->value, WebhookEventType::cases()), true);
    }

    /**
     * Get all supported payload type prefixes
     */
    public static function getSupportedTypes(): array
    {
        return array_keys(self::PAYLOAD_TYPE_MAP);
    }

    /**
     * Get the payload class for a given event type
     */
    private static function _getPayloadClass(string $type): string
    {
        foreach (self::PAYLOAD_TYPE_MAP as $prefix => $class) {
            if (str_starts_with($type, $prefix)) {
                return $class;
            }
        }

        return GenericPayload::class;
    }
}
