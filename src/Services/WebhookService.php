<?php

namespace PreludeSo\SDK\Services;

use DateTimeImmutable;
use PreludeSo\SDK\Enums\WebhookEventType;
use PreludeSo\SDK\Exceptions\PreludeException;
use PreludeSo\SDK\ValueObjects\Webhook\AbstractEventPayload;
use PreludeSo\SDK\ValueObjects\Webhook\Event;
use PreludeSo\SDK\ValueObjects\Webhook\EventPayloadFactory;

/**
 * Webhook Service for processing webhook events from Prelude
 */
final class WebhookService
{
    /**
     * Parse webhook data into an Event object with typed payload
     * 
     * @param array $webhookData Raw webhook data from request
     * @return Event
     * @throws PreludeException
     */
    public function parseWebhookData(array $webhookData): Event
    {
        $this->validateWebhookData($webhookData);
        
        try {
            $event = new Event(
                $webhookData['id'],
                $webhookData['type'],
                new DateTimeImmutable($webhookData['created_at']),
                $webhookData['payload']
            );
            
            return $event;
        } catch (\Throwable $e) {
            throw new PreludeException(
                sprintf('Failed to parse webhook data: %s', $e->getMessage()),
                0,
                $e
            );
        }
    }
    
    /**
     * Parse webhook payload into a typed payload object
     * 
     * @param array $payload Raw payload data
     * @param string $eventType Event type for determining payload class
     * @return AbstractEventPayload
     * @throws PreludeException
     */
    public function parseWebhookPayload(array $payload, string $eventType): AbstractEventPayload
    {
        try {
            return EventPayloadFactory::create($payload, $eventType);
        } catch (\Throwable $e) {
            throw new PreludeException(
                sprintf('Failed to parse webhook payload: %s', $e->getMessage()),
                0,
                $e
            );
        }
    }
    
    /**
     * Process complete webhook data and return both Event and typed payload
     * 
     * @param array $webhookData Raw webhook data from request
     * @return array{event: Event, payload: AbstractEventPayload}
     * @throws PreludeException
     */
    public function processWebhook(array $webhookData): array
    {
        $event = $this->parseWebhookData($webhookData);
        $payload = $this->parseWebhookPayload($webhookData['payload'], $webhookData['type']);
        
        return [
            'event' => $event,
            'payload' => $payload
        ];
    }
    
    /**
     * Check if an event type is supported
     * 
     * @param string $eventType
     * @return bool
     */
    public function isEventTypeSupported(string $eventType): bool
    {
        return EventPayloadFactory::isSupported($eventType);
    }
    
    /**
     * Get all supported event type prefixes
     * 
     * @return array
     */
    public function getSupportedEventTypes(): array
    {
        return EventPayloadFactory::getSupportedTypes();
    }
    
    /**
     * Check if event type is a known WebhookEventType enum case
     * 
     * @param string $eventType
     * @return bool
     */
    public function isKnownEventType(string $eventType): bool
    {
        return in_array($eventType, array_map(fn($case) => $case->value, WebhookEventType::cases()), true);
    }
    
    /**
     * Get WebhookEventType enum case for event type if it exists
     * 
     * @param string $eventType
     * @return WebhookEventType|null
     */
    public function getEventTypeEnum(string $eventType): ?WebhookEventType
    {
        return WebhookEventType::tryFrom($eventType);
    }
    
    /**
     * Validate webhook data structure
     * 
     * @param array $webhookData
     * @throws PreludeException
     */
    private function validateWebhookData(array $webhookData): void
    {
        $requiredFields = ['id', 'type', 'created_at', 'payload'];
        
        foreach ($requiredFields as $field) {
            if (!isset($webhookData[$field])) {
                throw new PreludeException(sprintf('Missing required webhook field: %s', $field));
            }
        }
        
        if (empty($webhookData['id'])) {
            throw new PreludeException('Webhook ID cannot be empty');
        }
        
        if (empty($webhookData['type'])) {
            throw new PreludeException('Webhook type cannot be empty');
        }
        
        if (!is_array($webhookData['payload'])) {
            throw new PreludeException('Webhook payload must be an array');
        }
    }
}