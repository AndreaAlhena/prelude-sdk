<?php

namespace PreludeSo\SDK\ValueObjects\Webhook;

use DateTimeImmutable;
use PreludeSo\SDK\Enums\WebhookEventType;

class Event
{
    private DateTimeImmutable $_createdAt;
    private string $_id;
    private array $_payload;
    private string $_type;

    public function __construct(
        string $id,
        string $type,
        DateTimeImmutable $createdAt,
        array $payload = []
    ) {
        $this->_id = $id;
        $this->_type = $type;
        $this->_createdAt = $createdAt;
        $this->_payload = $payload;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->_createdAt;
    }

    public function getId(): string
    {
        return $this->_id;
    }

    public function getPayload(): array
    {
        return $this->_payload;
    }

    public function getType(): string
    {
        return $this->_type;
    }
    
    /**
     * Check if this event type is a known WebhookEventType enum case
     */
    public function isKnownEventType(): bool
    {
        return in_array($this->_type, array_map(fn($case) => $case->value, WebhookEventType::cases()), true);
    }
    
    /**
     * Get the WebhookEventType enum case if this event type matches one
     */
    public function getEventTypeEnum(): ?WebhookEventType
    {
        return WebhookEventType::tryFrom($this->_type);
    }
}
