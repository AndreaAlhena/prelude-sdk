<?php

namespace PreludeSo\SDK\Tests\ValueObjects\Webhook;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PreludeSo\SDK\Enums\WebhookEventType;
use PreludeSo\SDK\ValueObjects\Webhook\Event;

class EventTest extends TestCase
{
    public function testCanCreateEvent(): void
    {
        $id = 'evt_01jnh4zwabf1grfsaq955ej3mt';
        $type = 'verify.authentication';
        $createdAt = new DateTimeImmutable('2025-03-04T17:59:21.163921113Z');
        $payload = [
            'metadata' => ['correlation_id' => 'e9156dad-de79-4d47-9e6b-e0c40e9244a4'],
            'price' => ['amount' => 0.009, 'currency' => 'EUR'],
            'target' => ['type' => 'phone_number', 'value' => '+33xxxxxxxx'],
            'verification_id' => 'vrf_01jnh4zt8vfq5r71n1sx9yvx5m'
        ];

        $event = new Event($id, $type, $createdAt, $payload);

        $this->assertSame($id, $event->getId());
        $this->assertSame($type, $event->getType());
        $this->assertSame($createdAt, $event->getCreatedAt());
        $this->assertSame($payload, $event->getPayload());
    }

    public function testCanCreateEventWithDifferentTypes(): void
    {
        $events = [
            ['evt_01jnh4zwabf1grfsaq955ej3mt', 'verify.authentication'],
            ['evt_01jnh50110f1gt7n74yb6kcrzb', 'verify.attempt'],
            ['evt_01jnh500a6frv9ky0wn4r6aycv', 'verify.delivery_status'],
        ];

        foreach ($events as [$id, $type]) {
            $createdAt = new DateTimeImmutable();
            $event = new Event($id, $type, $createdAt);

            $this->assertSame($id, $event->getId());
            $this->assertSame($type, $event->getType());
            $this->assertSame($createdAt, $event->getCreatedAt());
        }
    }

    public function testCreatedAtIsImmutable(): void
    {
        $createdAt = new DateTimeImmutable('2025-03-04T17:59:21.163921113Z');
        $event = new Event('evt_123', 'test.event', $createdAt);

        $retrievedCreatedAt = $event->getCreatedAt();
        $this->assertInstanceOf(DateTimeImmutable::class, $retrievedCreatedAt);
        $this->assertEquals($createdAt->format('c'), $retrievedCreatedAt->format('c'));
    }

    public function testCanCreateEventWithoutPayload(): void
    {
        $id = 'evt_01jnh4zwabf1grfsaq955ej3mt';
        $type = 'verify.authentication';
        $createdAt = new DateTimeImmutable('2025-03-04T17:59:21.163921113Z');

        $event = new Event($id, $type, $createdAt);

        $this->assertSame($id, $event->getId());
        $this->assertSame($type, $event->getType());
        $this->assertSame($createdAt, $event->getCreatedAt());
        $this->assertSame([], $event->getPayload());
    }

    public function testPayloadIsGenericArray(): void
    {
        $payloads = [
            // Verify authentication payload
            [
                'metadata' => ['correlation_id' => 'e9156dad-de79-4d47-9e6b-e0c40e9244a4'],
                'price' => ['amount' => 0.009, 'currency' => 'EUR'],
                'target' => ['type' => 'phone_number', 'value' => '+33xxxxxxxx'],
                'verification_id' => 'vrf_01jnh4zt8vfq5r71n1sx9yvx5m'
            ],
            // Verify attempt payload
            [
                'carrier_information' => ['mcc' => '208', 'mnc' => '10'],
                'delivery_status' => 'delivered',
                'id' => 'att_3v9s0v9gzt8hws0cp753q4gj0c',
                'metadata' => ['correlation_id' => 'e9156dad-de79-4d47-9e6b-e0c40e9244a4'],
                'verification_id' => 'vrf_01jnh4zt8vfq5r71n1sx9yvx5m'
            ],
            // Custom payload structure
            [
                'custom_field' => 'custom_value',
                'nested' => ['deep' => ['value' => 123]],
                'array_field' => [1, 2, 3]
            ]
        ];

        foreach ($payloads as $index => $payload) {
            $event = new Event(
                'evt_' . $index,
                'test.event',
                new DateTimeImmutable(),
                $payload
            );

            $this->assertSame($payload, $event->getPayload());
        }
    }
    
    public function testIsKnownEventTypeReturnsTrueForKnownTypes(): void
    {
        $knownTypes = [
            'verify.authentication',
            'verify.attempt',
            'verify.delivery_status',
            'transactional.message.created',
            'transactional.message.delivered',
            'transactional.message.failed',
            'transactional.message.pending_delivery'
        ];
        
        foreach ($knownTypes as $type) {
            $event = new Event('evt_123', $type, new DateTimeImmutable());
            $this->assertTrue($event->isKnownEventType(), "Type '{$type}' should be known");
        }
    }
    
    public function testIsKnownEventTypeReturnsFalseForUnknownTypes(): void
    {
        $unknownTypes = [
            'custom.event.type',
            'unknown.type',
            'verify.unknown',
            'transactional.unknown'
        ];
        
        foreach ($unknownTypes as $type) {
            $event = new Event('evt_123', $type, new DateTimeImmutable());
            $this->assertFalse($event->isKnownEventType(), "Type '{$type}' should not be known");
        }
    }
    
    public function testGetEventTypeEnumReturnsCorrectEnum(): void
    {
        $typeEnumPairs = [
            ['verify.authentication', WebhookEventType::VERIFY_AUTHENTICATION],
            ['verify.attempt', WebhookEventType::VERIFY_ATTEMPT],
            ['verify.delivery_status', WebhookEventType::VERIFY_DELIVERY_STATUS],
            ['transactional.message.created', WebhookEventType::TRANSACTIONAL_MESSAGE_CREATED],
            ['transactional.message.delivered', WebhookEventType::TRANSACTIONAL_MESSAGE_DELIVERED],
            ['transactional.message.failed', WebhookEventType::TRANSACTIONAL_MESSAGE_FAILED],
            ['transactional.message.pending_delivery', WebhookEventType::TRANSACTIONAL_MESSAGE_PENDING_DELIVERY]
        ];
        
        foreach ($typeEnumPairs as [$type, $expectedEnum]) {
            $event = new Event('evt_123', $type, new DateTimeImmutable());
            $this->assertSame($expectedEnum, $event->getEventTypeEnum());
        }
    }
    
    public function testGetEventTypeEnumReturnsNullForUnknownTypes(): void
    {
        $unknownTypes = [
            'custom.event.type',
            'unknown.type',
            'verify.unknown',
            'transactional.unknown'
        ];
        
        foreach ($unknownTypes as $type) {
            $event = new Event('evt_123', $type, new DateTimeImmutable());
            $this->assertNull($event->getEventTypeEnum(), "Type '{$type}' should return null enum");
        }
    }
}