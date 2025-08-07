<?php

namespace PreludeSo\SDK\Tests\ValueObjects\Webhook;

use PHPUnit\Framework\TestCase;
use PreludeSo\SDK\ValueObjects\Webhook\GenericPayload;

class GenericPayloadTest extends TestCase
{
    private array $samplePayload;

    protected function setUp(): void
    {
        $this->samplePayload = [
            'event_type' => 'custom.event',
            'id' => 'event_123',
            'nested' => [
                'field' => 'value',
                'number' => 42
            ],
            'timestamp' => '2023-12-01T10:30:00Z'
        ];
    }

    public function testCanCreateGenericPayload(): void
    {
        $payload = new GenericPayload($this->samplePayload);
        
        $this->assertInstanceOf(GenericPayload::class, $payload);
    }

    public function testCanGetData(): void
    {
        $payload = new GenericPayload($this->samplePayload);
        
        $this->assertSame($this->samplePayload, $payload->getData());
    }

    public function testCanGetSpecificValue(): void
    {
        $payload = new GenericPayload($this->samplePayload);
        
        $this->assertSame('custom.event', $payload->get('event_type'));
        $this->assertSame('event_123', $payload->get('id'));
        $this->assertSame($this->samplePayload['nested'], $payload->get('nested'));
    }

    public function testCanGetValueWithDefault(): void
    {
        $payload = new GenericPayload($this->samplePayload);
        
        $this->assertSame('default_value', $payload->get('non_existent', 'default_value'));
        $this->assertNull($payload->get('non_existent'));
    }

    public function testHasMethod(): void
    {
        $payload = new GenericPayload($this->samplePayload);
        
        $this->assertTrue($payload->has('event_type'));
        $this->assertTrue($payload->has('nested'));
        $this->assertFalse($payload->has('non_existent'));
    }

    public function testToArray(): void
    {
        $payload = new GenericPayload($this->samplePayload);
        
        $this->assertSame($this->samplePayload, $payload->toArray());
    }

    public function testGetRawPayload(): void
    {
        $payload = new GenericPayload($this->samplePayload);
        
        $this->assertSame($this->samplePayload, $payload->getRawPayload());
    }

    public function testCanHandleEmptyPayload(): void
    {
        $payload = new GenericPayload([]);
        
        $this->assertSame([], $payload->getData());
        $this->assertSame([], $payload->toArray());
        $this->assertFalse($payload->has('any_key'));
        $this->assertNull($payload->get('any_key'));
    }

    public function testCanHandleComplexNestedStructures(): void
    {
        $complexPayload = [
            'level1' => [
                'level2' => [
                    'level3' => [
                        'deep_value' => 'found'
                    ]
                ]
            ],
            'array_data' => [
                ['item' => 1],
                ['item' => 2],
                ['item' => 3]
            ]
        ];
        
        $payload = new GenericPayload($complexPayload);
        
        $this->assertSame($complexPayload, $payload->getData());
        $this->assertSame($complexPayload['level1'], $payload->get('level1'));
        $this->assertSame($complexPayload['array_data'], $payload->get('array_data'));
    }
}