<?php

namespace PreludeSo\SDK\Tests\ValueObjects\Webhook;

use PHPUnit\Framework\TestCase;
use PreludeSo\SDK\ValueObjects\Webhook\Price;
use PreludeSo\SDK\ValueObjects\Webhook\VerifyPayload;

class VerifyPayloadTest extends TestCase
{
    private array $samplePayload;

    protected function setUp(): void
    {
        $this->samplePayload = [
            'attempt_id' => 'attempt_123',
            'carrier_information' => [
                'name' => 'Verizon',
                'country' => 'US'
            ],
            'correlation_id' => 'corr_456',
            'delivery_status' => 'delivered',
            'price' => [
                'amount' => 0.05,
                'currency' => 'USD'
            ],
            'status' => 'verified',
            'target' => '+1234567890',
            'time' => '2023-12-01T10:30:00Z',
            'verification_id' => 'verify_789'
        ];
    }

    public function testCanCreateVerifyPayload(): void
    {
        $payload = new VerifyPayload($this->samplePayload);
        
        $this->assertSame('attempt_123', $payload->getAttemptId());
        $this->assertSame($this->samplePayload['carrier_information'], $payload->getCarrierInformation());
        $this->assertSame('corr_456', $payload->getCorrelationId());
        $this->assertSame('delivered', $payload->getDeliveryStatus());
        $this->assertSame('verified', $payload->getStatus());
        $this->assertSame('+1234567890', $payload->getTarget());
        $this->assertSame('verify_789', $payload->getVerificationId());
    }

    public function testCanGetPrice(): void
    {
        $payload = new VerifyPayload($this->samplePayload);
        $price = $payload->getPrice();
        
        $this->assertInstanceOf(Price::class, $price);
        $this->assertSame(0.05, $price->getAmount());
        $this->assertSame('USD', $price->getCurrency());
    }

    public function testCanGetTime(): void
    {
        $payload = new VerifyPayload($this->samplePayload);
        $time = $payload->getTime();
        
        $this->assertInstanceOf(\DateTimeImmutable::class, $time);
        $this->assertSame('2023-12-01T10:30:00+00:00', $time->format('c'));
    }

    public function testCanHandleMissingOptionalFields(): void
    {
        $minimalPayload = [
            'verification_id' => 'verify_123',
            'target' => '+1234567890',
            'status' => 'pending'
        ];
        
        $payload = new VerifyPayload($minimalPayload);
        
        $this->assertNull($payload->getAttemptId());
        $this->assertNull($payload->getCarrierInformation());
        $this->assertNull($payload->getCorrelationId());
        $this->assertNull($payload->getDeliveryStatus());
        $this->assertNull($payload->getPrice());
        $this->assertNull($payload->getTime());
        $this->assertSame('verify_123', $payload->getVerificationId());
        $this->assertSame('+1234567890', $payload->getTarget());
        $this->assertSame('pending', $payload->getStatus());
    }

    public function testToArray(): void
    {
        $payload = new VerifyPayload($this->samplePayload);
        $result = $payload->toArray();
        
        $this->assertIsArray($result);
        $this->assertSame('attempt_123', $result['attempt_id']);
        $this->assertSame('corr_456', $result['correlation_id']);
        $this->assertSame('delivered', $result['delivery_status']);
        $this->assertSame('verified', $result['status']);
        $this->assertSame('+1234567890', $result['target']);
        $this->assertSame('verify_789', $result['verification_id']);
        $this->assertSame($this->samplePayload['carrier_information'], $result['carrier_information']);
        
        // Check price array
        $this->assertIsArray($result['price']);
        $this->assertSame(0.05, $result['price']['amount']);
        $this->assertSame('USD', $result['price']['currency']);
        
        // Check time string
        $this->assertSame('2023-12-01T10:30:00+00:00', $result['time']);
    }

    public function testGetRawPayload(): void
    {
        $payload = new VerifyPayload($this->samplePayload);
        
        $this->assertSame($this->samplePayload, $payload->getRawPayload());
    }

    public function testHandlesInvalidTimeFormat(): void
    {
        $invalidPayload = array_merge($this->samplePayload, [
            'time' => 'invalid-date-format'
        ]);
        
        $payload = new VerifyPayload($invalidPayload);
        
        $this->assertNull($payload->getTime());
    }
}