<?php

namespace PreludeSo\SDK\Tests\ValueObjects\Webhook;

use PHPUnit\Framework\TestCase;
use PreludeSo\SDK\Enums\WebhookEventType;
use PreludeSo\SDK\ValueObjects\Webhook\EventPayloadFactory;
use PreludeSo\SDK\ValueObjects\Webhook\GenericPayload;
use PreludeSo\SDK\ValueObjects\Webhook\TransactionalPayload;
use PreludeSo\SDK\ValueObjects\Webhook\VerifyPayload;

class EventPayloadFactoryTest extends TestCase
{
    public function testCanCreateVerifyPayload(): void
    {
        $payload = ['verification_id' => 'verify_123', 'target' => '+1234567890', 'status' => 'verified'];
        $result = EventPayloadFactory::create($payload, 'verify.completed');
        
        $this->assertInstanceOf(VerifyPayload::class, $result);
        /** @var VerifyPayload $result */
        $this->assertSame('verify_123', $result->getVerificationId());
    }

    public function testCanCreateTransactionalPayload(): void
    {
        $payload = ['id' => 'trans_123', 'to' => '+1234567890', 'status' => 'sent'];
        $result = EventPayloadFactory::create($payload, 'transactional.sent');
        
        $this->assertInstanceOf(TransactionalPayload::class, $result);
        /** @var TransactionalPayload $result */
        $this->assertSame('trans_123', $result->getId());
    }

    public function testCanCreateGenericPayloadForUnsupportedType(): void
    {
        $payload = ['some_field' => 'some_value'];
        $result = EventPayloadFactory::create($payload, 'unknown.type');
        
        $this->assertInstanceOf(GenericPayload::class, $result);
        /** @var GenericPayload $result */
        $this->assertSame('some_value', $result->get('some_field'));
    }

    public function testThrowsExceptionForEmptyType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Event type cannot be empty');
        
        EventPayloadFactory::create([], '');
    }

    public function testThrowsExceptionWhenPayloadCreationFails(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Failed to create payload for type "verify\.test"/');
        
        // This should trigger an error in VerifyPayload validation
        EventPayloadFactory::create(['invalid' => 'data'], 'verify.test');
    }

    public function testIsSupportedReturnsTrueForVerifyTypes(): void
    {
        $this->assertTrue(EventPayloadFactory::isSupported('verify.completed'));
        $this->assertTrue(EventPayloadFactory::isSupported('verify.failed'));
        $this->assertTrue(EventPayloadFactory::isSupported('verify.'));
    }

    public function testIsSupportedReturnsTrueForTransactionalTypes(): void
    {
        $this->assertTrue(EventPayloadFactory::isSupported('transactional.sent'));
        $this->assertTrue(EventPayloadFactory::isSupported('transactional.delivered'));
        $this->assertTrue(EventPayloadFactory::isSupported('transactional.'));
    }

    public function testIsSupportedReturnsFalseForUnsupportedTypes(): void
    {
        $this->assertFalse(EventPayloadFactory::isSupported('unknown.type'));
        $this->assertFalse(EventPayloadFactory::isSupported('lookup.completed'));
        $this->assertFalse(EventPayloadFactory::isSupported(''));
    }

    public function testGetSupportedTypesReturnsCorrectPrefixes(): void
    {
        $supportedTypes = EventPayloadFactory::getSupportedTypes();
        
        $this->assertIsArray($supportedTypes);
        $this->assertContains('verify.', $supportedTypes);
        $this->assertContains('transactional.', $supportedTypes);
        $this->assertCount(2, $supportedTypes);
    }

    public function testVerifyPayloadTypesMapping(): void
    {
        $verifyTypes = [
            'verify.completed',
            'verify.failed',
            'verify.expired',
            'verify.cancelled'
        ];
        
        foreach ($verifyTypes as $type) {
            $payload = ['verification_id' => 'test', 'target' => '+1234567890', 'status' => 'verified'];
            $result = EventPayloadFactory::create($payload, $type);
            $this->assertInstanceOf(VerifyPayload::class, $result);
        }
    }

    public function testTransactionalPayloadTypesMapping(): void
    {
        $transactionalTypes = [
            'transactional.sent',
            'transactional.delivered',
            'transactional.failed',
            'transactional.expired'
        ];
        
        foreach ($transactionalTypes as $type) {
            $payload = ['id' => 'test', 'to' => '+1234567890', 'status' => 'sent'];
            $result = EventPayloadFactory::create($payload, $type);
            $this->assertInstanceOf(TransactionalPayload::class, $result);
        }
    }
    
    public function testIsSupportedReturnsTrueForKnownEnumTypes(): void
    {
        $knownTypes = [
            WebhookEventType::VERIFY_AUTHENTICATION->value,
            WebhookEventType::VERIFY_ATTEMPT->value,
            WebhookEventType::VERIFY_DELIVERY_STATUS->value,
            WebhookEventType::TRANSACTIONAL_MESSAGE_CREATED->value,
            WebhookEventType::TRANSACTIONAL_MESSAGE_DELIVERED->value,
            WebhookEventType::TRANSACTIONAL_MESSAGE_FAILED->value,
            WebhookEventType::TRANSACTIONAL_MESSAGE_PENDING_DELIVERY->value
        ];
        
        foreach ($knownTypes as $type) {
            $this->assertTrue(
                EventPayloadFactory::isSupported($type),
                "Known enum type '{$type}' should be supported"
            );
        }
    }
    
    public function testIsSupportedWorksWithEnumValues(): void
    {
        // Test that enum values work correctly with the factory
        $verifyPayload = ['verification_id' => 'test', 'target' => '+1234567890', 'status' => 'verified'];
        $transactionalPayload = ['id' => 'test', 'to' => '+1234567890', 'status' => 'sent'];
        
        // Test verify enum types
        $verifyResult = EventPayloadFactory::create(
            $verifyPayload,
            WebhookEventType::VERIFY_AUTHENTICATION->value
        );
        $this->assertInstanceOf(VerifyPayload::class, $verifyResult);
        
        // Test transactional enum types
        $transactionalResult = EventPayloadFactory::create(
            $transactionalPayload,
            WebhookEventType::TRANSACTIONAL_MESSAGE_CREATED->value
        );
        $this->assertInstanceOf(TransactionalPayload::class, $transactionalResult);
    }
    
    public function testCreatePayloadWithAllEnumTypes(): void
    {
        $verifyPayload = ['verification_id' => 'test', 'target' => '+1234567890', 'status' => 'verified'];
        $transactionalPayload = ['id' => 'test', 'to' => '+1234567890', 'status' => 'sent'];
        
        // Test all verify enum cases
        $verifyEnums = [
            WebhookEventType::VERIFY_AUTHENTICATION,
            WebhookEventType::VERIFY_ATTEMPT,
            WebhookEventType::VERIFY_DELIVERY_STATUS
        ];
        
        foreach ($verifyEnums as $enumCase) {
            $result = EventPayloadFactory::create($verifyPayload, $enumCase->value);
            $this->assertInstanceOf(VerifyPayload::class, $result, "Failed for enum: {$enumCase->name}");
        }
        
        // Test all transactional enum cases
        $transactionalEnums = [
            WebhookEventType::TRANSACTIONAL_MESSAGE_CREATED,
            WebhookEventType::TRANSACTIONAL_MESSAGE_DELIVERED,
            WebhookEventType::TRANSACTIONAL_MESSAGE_FAILED,
            WebhookEventType::TRANSACTIONAL_MESSAGE_PENDING_DELIVERY
        ];
        
        foreach ($transactionalEnums as $enumCase) {
            $result = EventPayloadFactory::create($transactionalPayload, $enumCase->value);
            $this->assertInstanceOf(TransactionalPayload::class, $result, "Failed for enum: {$enumCase->name}");
        }
    }
}