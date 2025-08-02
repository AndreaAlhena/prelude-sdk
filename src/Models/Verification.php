<?php

namespace Prelude\SDK\Models;

use Prelude\SDK\Enums\Channel;
use Prelude\SDK\Enums\VerificationMethod;
use Prelude\SDK\Enums\VerificationReason;
use Prelude\SDK\Enums\VerificationStatus;
use Prelude\SDK\ValueObjects\Verify\Metadata;
use Prelude\SDK\ValueObjects\Verify\Silent;

/**
 * Verification model
 */
class Verification
{
    private array $_channels;
    private string $_id;
    private ?Metadata $_metadata;
    private VerificationMethod $_method;
    private array $_rawData;
    private ?VerificationReason $_reason;
    private string $_requestId;
    private ?Silent $_silent;
    private VerificationStatus $_status;
    
    /**
     * Create a new verification instance
     * 
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->_rawData = $data;
        $this->_channels = isset($data['channels']) 
            ? array_map(fn($channel) => Channel::from($channel), $data['channels']) 
            : [];
        $this->_id = $data['id'] ?? '';
        $this->_metadata = isset($data['metadata']) ? Metadata::fromArray($data['metadata']) : null;
        $this->_method = VerificationMethod::from($data['method'] ?? 'message');
        $this->_reason = isset($data['reason']) ? VerificationReason::from($data['reason']) : null;
        $this->_requestId = $data['request_id'] ?? '';
        $this->_silent = isset($data['silent']) ? Silent::fromArray($data['silent']) : null;
        $this->_status = VerificationStatus::from($data['status'] ?? 'success');
    }
    
    /**
     * Get the ordered sequence of channels
     * 
     * @return Channel[]
     */
    public function getChannels(): array
    {
        return $this->_channels;
    }
    
    /**
     * Get the verification ID
     * 
     * @return string
     */
    public function getId(): string
    {
        return $this->_id;
    }
    
    /**
     * Get the metadata
     * 
     * @return Metadata|null
     */
    public function getMetadata(): ?Metadata
    {
        return $this->_metadata;
    }
    
    /**
     * Get the verification method
     * 
     * @return VerificationMethod
     */
    public function getMethod(): VerificationMethod
    {
        return $this->_method;
    }
    
    /**
     * Get the raw response data
     * 
     * @return array
     */
    public function getRawData(): array
    {
        return $this->_rawData;
    }
    
    /**
     * Get the reason for blocked verification
     * 
     * @return VerificationReason|null
     */
    public function getReason(): ?VerificationReason
    {
        return $this->_reason;
    }
    
    /**
     * Get the request ID
     * 
     * @return string
     */
    public function getRequestId(): string
    {
        return $this->_requestId;
    }
    
    /**
     * Get the silent verification properties
     * 
     * @return Silent|null
     */
    public function getSilent(): ?Silent
    {
        return $this->_silent;
    }
    
    /**
     * Get the verification status
     * 
     * @return VerificationStatus
     */
    public function getStatus(): VerificationStatus
    {
        return $this->_status;
    }
    
    /**
     * Check if the verification is blocked
     * 
     * @return bool
     */
    public function isBlocked(): bool
    {
        return $this->_status === VerificationStatus::BLOCKED;
    }
    
    /**
     * Check if the verification is successful
     * 
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->_status === VerificationStatus::SUCCESS;
    }
    
    /**
     * Check if the verification should be retried
     * 
     * @return bool
     */
    public function shouldRetry(): bool
    {
        return $this->_status === VerificationStatus::RETRY;
    }
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        $result = [
            'id' => $this->_id,
            'status' => $this->_status->value,
            'method' => $this->_method->value,
            'channels' => array_map(fn($channel) => $channel->value, $this->_channels),
            'request_id' => $this->_requestId
        ];
        
        if ($this->_reason !== null) {
            $result['reason'] = $this->_reason->value;
        }
        
        if ($this->_silent !== null) {
            $result['silent'] = $this->_silent->toArray();
        }
        
        if ($this->_metadata !== null) {
            $result['metadata'] = $this->_metadata->toArray();
        }
        
        return $result;
    }
}