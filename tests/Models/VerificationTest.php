<?php

namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use Prelude\SDK\Models\Verification;
use Prelude\SDK\Enums\Channel;
use Prelude\SDK\Enums\VerificationStatus;
use Prelude\SDK\Enums\VerificationMethod;
use Prelude\SDK\Enums\VerificationReason;
use Prelude\SDK\ValueObjects\Shared\Metadata;
use Prelude\SDK\ValueObjects\Verify\Silent;

class VerificationTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated_with_minimal_data()
    {
        $data = [
            'id' => 'ver_123',
            'status' => 'success',
            'method' => 'message',
            'request_id' => 'req_456'
        ];
        
        $verification = new Verification($data);
        
        $this->assertEquals('ver_123', $verification->getId());
        $this->assertEquals(VerificationStatus::SUCCESS, $verification->getStatus());
        $this->assertEquals(VerificationMethod::MESSAGE, $verification->getMethod());
        $this->assertEquals('req_456', $verification->getRequestId());
        $this->assertNull($verification->getReason());
        $this->assertEmpty($verification->getChannels());
        $this->assertNull($verification->getSilent());
        $this->assertNull($verification->getMetadata());
    }
    
    /** @test */
    public function it_can_be_instantiated_with_full_data()
    {
        $data = [
            'id' => 'ver_123',
            'status' => 'blocked',
            'method' => 'voice',
            'reason' => 'invalid_phone_number',
            'channels' => ['sms', 'voice', 'whatsapp'],
            'silent' => [
                'enabled' => true,
                'phone_number' => '+1234567890'
            ],
            'metadata' => [
                'correlation_id' => 'corr_789'
            ],
            'request_id' => 'req_456'
        ];
        
        $verification = new Verification($data);
        
        $this->assertEquals('ver_123', $verification->getId());
        $this->assertEquals(VerificationStatus::BLOCKED, $verification->getStatus());
        $this->assertEquals(VerificationMethod::VOICE, $verification->getMethod());
        $this->assertEquals(VerificationReason::INVALID_PHONE_NUMBER, $verification->getReason());
        
        $channels = $verification->getChannels();
        $this->assertCount(3, $channels);
        $this->assertEquals(Channel::SMS, $channels[0]);
        $this->assertEquals(Channel::VOICE, $channels[1]);
        $this->assertEquals(Channel::WHATSAPP, $channels[2]);
        
        $silent = $verification->getSilent();
        $this->assertInstanceOf(Silent::class, $silent);
        $this->assertTrue($silent->isEnabled());
        $this->assertEquals('+1234567890', $silent->getPhoneNumber());
        
        $metadata = $verification->getMetadata();
        $this->assertInstanceOf(Metadata::class, $metadata);
        
        $this->assertEquals('req_456', $verification->getRequestId());
    }
    
    /** @test */
    public function it_has_status_check_methods()
    {
        $successData = ['id' => 'ver_1', 'status' => 'success', 'method' => 'message', 'request_id' => 'req_1'];
        $retryData = ['id' => 'ver_2', 'status' => 'retry', 'method' => 'message', 'request_id' => 'req_2'];
        $blockedData = ['id' => 'ver_3', 'status' => 'blocked', 'method' => 'message', 'request_id' => 'req_3'];
        
        $successVerification = new Verification($successData);
        $retryVerification = new Verification($retryData);
        $blockedVerification = new Verification($blockedData);
        
        $this->assertTrue($successVerification->isSuccess());
        $this->assertFalse($successVerification->shouldRetry());
        $this->assertFalse($successVerification->isBlocked());
        
        $this->assertFalse($retryVerification->isSuccess());
        $this->assertTrue($retryVerification->shouldRetry());
        $this->assertFalse($retryVerification->isBlocked());
        
        $this->assertFalse($blockedVerification->isSuccess());
        $this->assertFalse($blockedVerification->shouldRetry());
        $this->assertTrue($blockedVerification->isBlocked());
    }
    
    /** @test */
    public function it_converts_to_array_correctly()
    {
        $data = [
            'id' => 'ver_123',
            'status' => 'success',
            'method' => 'message',
            'channels' => ['sms', 'whatsapp'],
            'silent' => [
                'enabled' => true,
                'phone_number' => '+1234567890'
            ],
            'metadata' => [
                'correlation_id' => 'corr_789'
            ],
            'request_id' => 'req_456'
        ];
        
        $verification = new Verification($data);
        $array = $verification->toArray();
        
        $this->assertEquals('ver_123', $array['id']);
        $this->assertEquals('success', $array['status']);
        $this->assertEquals('message', $array['method']);
        $this->assertEquals(['sms', 'whatsapp'], $array['channels']);
        $this->assertEquals('req_456', $array['request_id']);
        
        $this->assertArrayHasKey('silent', $array);
        $this->assertEquals(true, $array['silent']['enabled']);
        $this->assertEquals('+1234567890', $array['silent']['phone_number']);
        
        $this->assertArrayHasKey('metadata', $array);
        $this->assertEquals('corr_789', $array['metadata']['correlation_id']);
    }
    
    /** @test */
    public function it_excludes_null_optional_fields_from_array()
    {
        $data = [
            'id' => 'ver_123',
            'status' => 'success',
            'method' => 'message',
            'request_id' => 'req_456'
        ];
        
        $verification = new Verification($data);
        $array = $verification->toArray();
        
        $this->assertArrayNotHasKey('reason', $array);
        $this->assertArrayNotHasKey('silent', $array);
        $this->assertArrayNotHasKey('metadata', $array);
    }
}