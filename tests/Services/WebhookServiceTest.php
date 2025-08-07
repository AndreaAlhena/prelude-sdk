<?php

namespace PreludeSo\SDK\Tests\Services;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PreludeSo\SDK\Enums\WebhookEventType;
use PreludeSo\SDK\Exceptions\PreludeException;
use PreludeSo\SDK\Services\WebhookService;
use PreludeSo\SDK\ValueObjects\Webhook\Event;
use PreludeSo\SDK\ValueObjects\Webhook\GenericPayload;
use PreludeSo\SDK\ValueObjects\Webhook\TransactionalPayload;
use PreludeSo\SDK\ValueObjects\Webhook\VerifyPayload;

class WebhookServiceTest extends TestCase
{
    private WebhookService $webhookService;
    
    protected function setUp(): void
    {
        $this->webhookService = new WebhookService();
    }
    
    public function test_parseWebhookData_creates_event_object(): void
    {
        $webhookData = [
            'id' => 'evt_01jnh4zwabf1grfsaq955ej3mt',
            'type' => 'verify.authentication',
            'created_at' => '2025-03-04T17:59:21.163921113Z',
            'payload' => [
                'verification_id' => 'vrf_01jnh4zt8vfq5r71n1sx9yvx5m',
                'target' => ['type' => 'phone_number', 'value' => '+33xxxxxxxx']
            ]
        ];
        
        $event = $this->webhookService->parseWebhookData($webhookData);
        
        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals('evt_01jnh4zwabf1grfsaq955ej3mt', $event->getId());
        $this->assertEquals('verify.authentication', $event->getType());
        $this->assertInstanceOf(DateTimeImmutable::class, $event->getCreatedAt());
        $this->assertEquals($webhookData['payload'], $event->getPayload());
    }
    
    public function test_parseWebhookData_throws_exception_for_missing_required_fields(): void
    {
        $this->expectException(PreludeException::class);
        $this->expectExceptionMessage('Missing required webhook field: id');
        
        $webhookData = [
            'type' => 'verify.authentication',
            'created_at' => '2025-03-04T17:59:21.163921113Z',
            'payload' => []
        ];
        
        $this->webhookService->parseWebhookData($webhookData);
    }
    
    public function test_parseWebhookData_throws_exception_for_empty_id(): void
    {
        $this->expectException(PreludeException::class);
        $this->expectExceptionMessage('Webhook ID cannot be empty');
        
        $webhookData = [
            'id' => '',
            'type' => 'verify.authentication',
            'created_at' => '2025-03-04T17:59:21.163921113Z',
            'payload' => []
        ];
        
        $this->webhookService->parseWebhookData($webhookData);
    }
    
    public function test_parseWebhookData_throws_exception_for_empty_type(): void
    {
        $this->expectException(PreludeException::class);
        $this->expectExceptionMessage('Webhook type cannot be empty');
        
        $webhookData = [
            'id' => 'evt_123',
            'type' => '',
            'created_at' => '2025-03-04T17:59:21.163921113Z',
            'payload' => []
        ];
        
        $this->webhookService->parseWebhookData($webhookData);
    }
    
    public function test_parseWebhookData_throws_exception_for_invalid_payload(): void
    {
        $this->expectException(PreludeException::class);
        $this->expectExceptionMessage('Webhook payload must be an array');
        
        $webhookData = [
            'id' => 'evt_123',
            'type' => 'verify.authentication',
            'created_at' => '2025-03-04T17:59:21.163921113Z',
            'payload' => 'invalid'
        ];
        
        $this->webhookService->parseWebhookData($webhookData);
    }
    
    public function test_parseWebhookPayload_creates_verify_payload(): void
    {
        $payload = [
            'verification_id' => 'vrf_01jnh4zt8vfq5r71n1sx9yvx5m',
            'target' => ['type' => 'phone_number', 'value' => '+33xxxxxxxx']
        ];
        
        $result = $this->webhookService->parseWebhookPayload($payload, 'verify.authentication');
        
        $this->assertInstanceOf(VerifyPayload::class, $result);
    }
    
    public function test_parseWebhookPayload_creates_transactional_payload(): void
    {
        $payload = [
            'id' => 'tx_01htjet67afxhta23j7dtekneh',
            'to' => '+3361234567',
            'created_at' => '2024-04-03T17:08:01.349000489Z'
        ];
        
        $result = $this->webhookService->parseWebhookPayload($payload, 'transactional.message.created');
        
        $this->assertInstanceOf(TransactionalPayload::class, $result);
    }
    
    public function test_parseWebhookPayload_creates_generic_payload_for_unknown_type(): void
    {
        $payload = [
            'custom_field' => 'custom_value',
            'nested_data' => ['key' => 'value']
        ];
        
        $result = $this->webhookService->parseWebhookPayload($payload, 'custom.event.type');
        
        $this->assertInstanceOf(GenericPayload::class, $result);
    }
    
    public function test_processWebhook_returns_event_and_payload(): void
    {
        $webhookData = [
            'id' => 'evt_01jnh4zwabf1grfsaq955ej3mt',
            'type' => 'verify.authentication',
            'created_at' => '2025-03-04T17:59:21.163921113Z',
            'payload' => [
                'verification_id' => 'vrf_01jnh4zt8vfq5r71n1sx9yvx5m',
                'target' => ['type' => 'phone_number', 'value' => '+33xxxxxxxx']
            ]
        ];
        
        $result = $this->webhookService->processWebhook($webhookData);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('event', $result);
        $this->assertArrayHasKey('payload', $result);
        $this->assertInstanceOf(Event::class, $result['event']);
        $this->assertInstanceOf(VerifyPayload::class, $result['payload']);
    }
    
    public function test_isEventTypeSupported_returns_true_for_supported_types(): void
    {
        $this->assertTrue($this->webhookService->isEventTypeSupported('verify.authentication'));
        $this->assertTrue($this->webhookService->isEventTypeSupported('transactional.message.created'));
        $this->assertFalse($this->webhookService->isEventTypeSupported('custom.event.type')); // Not explicitly supported, but can use GenericPayload
    }
    
    public function test_getSupportedEventTypes_returns_prefixes(): void
    {
        $supportedTypes = $this->webhookService->getSupportedEventTypes();
        
        $this->assertIsArray($supportedTypes);
        $this->assertContains('verify.', $supportedTypes);
        $this->assertContains('transactional.', $supportedTypes);
    }
    
    public function test_isKnownEventType_returns_true_for_enum_cases(): void
    {
        $this->assertTrue($this->webhookService->isKnownEventType('verify.authentication'));
        $this->assertTrue($this->webhookService->isKnownEventType('transactional.message.created'));
        $this->assertFalse($this->webhookService->isKnownEventType('custom.event.type'));
    }
    
    public function test_getEventTypeEnum_returns_enum_case(): void
    {
        $enum = $this->webhookService->getEventTypeEnum('verify.authentication');
        $this->assertEquals(WebhookEventType::VERIFY_AUTHENTICATION, $enum);
        
        $enum = $this->webhookService->getEventTypeEnum('transactional.message.created');
        $this->assertEquals(WebhookEventType::TRANSACTIONAL_MESSAGE_CREATED, $enum);
        
        $enum = $this->webhookService->getEventTypeEnum('custom.event.type');
        $this->assertNull($enum);
    }
    
    public function test_parseWebhookData_handles_invalid_datetime(): void
    {
        $this->expectException(PreludeException::class);
        $this->expectExceptionMessage('Failed to parse webhook data');
        
        $webhookData = [
            'id' => 'evt_123',
            'type' => 'verify.authentication',
            'created_at' => 'invalid-datetime',
            'payload' => []
        ];
        
        $this->webhookService->parseWebhookData($webhookData);
    }
    
    public function test_parseWebhookPayload_handles_invalid_payload(): void
    {
        $this->expectException(PreludeException::class);
        $this->expectExceptionMessage('Failed to parse webhook payload');
        
        // This should trigger an error in the payload constructor
        $payload = [];
        
        $this->webhookService->parseWebhookPayload($payload, '');
    }
}