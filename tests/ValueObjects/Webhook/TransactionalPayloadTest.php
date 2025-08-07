<?php

namespace PreludeSo\SDK\Tests\ValueObjects\Webhook;

use PHPUnit\Framework\TestCase;
use PreludeSo\SDK\ValueObjects\Webhook\Price;
use PreludeSo\SDK\ValueObjects\Webhook\TransactionalPayload;

class TransactionalPayloadTest extends TestCase
{
    private array $samplePayload;

    protected function setUp(): void
    {
        $this->samplePayload = [
            'correlation_id' => 'corr_123',
            'created_at' => '2023-12-01T10:30:00Z',
            'customer_uuid' => 'customer_456',
            'expires_at' => '2023-12-01T11:30:00Z',
            'id' => 'trans_789',
            'mcc' => '310',
            'message_id' => 'msg_abc',
            'mnc' => '260',
            'price' => [
                'amount' => 0.10,
                'currency' => 'USD'
            ],
            'segment_count' => '2',
            'status' => 'sent',
            'to' => '+1234567890',
            'variables' => [
                'name' => 'John',
                'code' => '123456'
            ]
        ];
    }

    public function testCanCreateTransactionalPayload(): void
    {
        $payload = new TransactionalPayload($this->samplePayload);
        
        $this->assertSame('corr_123', $payload->getCorrelationId());
        $this->assertSame('customer_456', $payload->getCustomerUuid());
        $this->assertSame('trans_789', $payload->getId());
        $this->assertSame('310', $payload->getMcc());
        $this->assertSame('msg_abc', $payload->getMessageId());
        $this->assertSame('260', $payload->getMnc());
        $this->assertSame(2, $payload->getSegmentCount());
        $this->assertSame('sent', $payload->getStatus());
        $this->assertSame('+1234567890', $payload->getTo());
        $this->assertSame($this->samplePayload['variables'], $payload->getVariables());
    }

    public function testCanGetPrice(): void
    {
        $payload = new TransactionalPayload($this->samplePayload);
        $price = $payload->getPrice();
        
        $this->assertInstanceOf(Price::class, $price);
        $this->assertSame(0.10, $price->getAmount());
        $this->assertSame('USD', $price->getCurrency());
    }

    public function testCanGetCreatedAt(): void
    {
        $payload = new TransactionalPayload($this->samplePayload);
        $createdAt = $payload->getCreatedAt();
        
        $this->assertInstanceOf(\DateTimeImmutable::class, $createdAt);
        $this->assertSame('2023-12-01T10:30:00+00:00', $createdAt->format('c'));
    }

    public function testCanGetExpiresAt(): void
    {
        $payload = new TransactionalPayload($this->samplePayload);
        $expiresAt = $payload->getExpiresAt();
        
        $this->assertInstanceOf(\DateTimeImmutable::class, $expiresAt);
        $this->assertSame('2023-12-01T11:30:00+00:00', $expiresAt->format('c'));
    }

    public function testCanHandleMissingOptionalFields(): void
    {
        $minimalPayload = [
            'id' => 'trans_123',
            'to' => '+1234567890',
            'status' => 'pending'
        ];
        
        $payload = new TransactionalPayload($minimalPayload);
        
        $this->assertNull($payload->getCorrelationId());
        $this->assertNull($payload->getCreatedAt());
        $this->assertNull($payload->getCustomerUuid());
        $this->assertNull($payload->getExpiresAt());
        $this->assertNull($payload->getMcc());
        $this->assertNull($payload->getMessageId());
        $this->assertNull($payload->getMnc());
        $this->assertNull($payload->getPrice());
        $this->assertSame(0, $payload->getSegmentCount());
        $this->assertNull($payload->getVariables());
        $this->assertSame('trans_123', $payload->getId());
        $this->assertSame('+1234567890', $payload->getTo());
        $this->assertSame('pending', $payload->getStatus());
    }

    public function testSegmentCountParsing(): void
    {
        // Test string to int conversion
        $payload1 = new TransactionalPayload(array_merge($this->samplePayload, ['segment_count' => '5']));
        $this->assertSame(5, $payload1->getSegmentCount());
        
        // Test int value
        $payload2 = new TransactionalPayload(array_merge($this->samplePayload, ['segment_count' => 3]));
        $this->assertSame(3, $payload2->getSegmentCount());
        
        // Test invalid value defaults to 0
        $payload3 = new TransactionalPayload(array_merge($this->samplePayload, ['segment_count' => 'invalid']));
        $this->assertSame(0, $payload3->getSegmentCount());
    }

    public function testToArray(): void
    {
        $payload = new TransactionalPayload($this->samplePayload);
        $result = $payload->toArray();
        
        $this->assertIsArray($result);
        $this->assertSame('corr_123', $result['correlation_id']);
        $this->assertSame('customer_456', $result['customer_uuid']);
        $this->assertSame('trans_789', $result['id']);
        $this->assertSame('310', $result['mcc']);
        $this->assertSame('msg_abc', $result['message_id']);
        $this->assertSame('260', $result['mnc']);
        $this->assertSame(2, $result['segment_count']);
        $this->assertSame('sent', $result['status']);
        $this->assertSame('+1234567890', $result['to']);
        $this->assertSame($this->samplePayload['variables'], $result['variables']);
        
        // Check price array
        $this->assertIsArray($result['price']);
        $this->assertSame(0.10, $result['price']['amount']);
        $this->assertSame('USD', $result['price']['currency']);
        
        // Check datetime strings
        $this->assertSame('2023-12-01T10:30:00+00:00', $result['created_at']);
        $this->assertSame('2023-12-01T11:30:00+00:00', $result['expires_at']);
    }

    public function testGetRawPayload(): void
    {
        $payload = new TransactionalPayload($this->samplePayload);
        
        $this->assertSame($this->samplePayload, $payload->getRawPayload());
    }

    public function testHandlesInvalidDateFormats(): void
    {
        $invalidPayload = array_merge($this->samplePayload, [
            'created_at' => 'invalid-date-format',
            'expires_at' => 'also-invalid'
        ]);
        
        $payload = new TransactionalPayload($invalidPayload);
        
        $this->assertNull($payload->getCreatedAt());
        $this->assertNull($payload->getExpiresAt());
    }
}