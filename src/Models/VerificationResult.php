<?php

namespace PreludeSo\SDK\Models;

use PreludeSo\SDK\Enums\Channel;
use PreludeSo\SDK\Enums\VerificationMethod;
use PreludeSo\SDK\Enums\VerificationReason;
use PreludeSo\SDK\Enums\VerificationStatus;
use PreludeSo\SDK\ValueObjects\Shared\Metadata;
use PreludeSo\SDK\ValueObjects\Verify\Silent;

/**
 * Verification Result model
 */
class VerificationResult
{
    /**
     * @var Channel[]
     */
    private array $_channels;
    private string $_id;
    private ?Metadata $_metadata;
    private VerificationMethod $_method;
    private array $_rawData;
    private ?VerificationReason $_reason;
    private ?string $_requestId;
    private ?Silent $_silent;
    private VerificationStatus $_status;
    
    /**
     * Create a new verification result instance
     * 
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->_rawData = $data;
        $this->_id = $data['id'] ?? '';
        $this->_status = VerificationStatus::from($data['status'] ?? 'retry');
        $this->_method = VerificationMethod::from($data['method'] ?? 'message');
        $this->_reason = isset($data['reason']) ? VerificationReason::from($data['reason']) : null;
        $this->_channels = $data['channels'] ?? [];
        $this->_silent = isset($data['silent']) ? Silent::fromArray($data['silent']) : null;
        $this->_metadata = isset($data['metadata']) ? Metadata::fromArray($data['metadata']) : null;
        $this->_requestId = $data['request_id'] ?? null;
    }
    
    /**
     * Get the channels
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
     * Get the verification reason (only present when status is blocked)
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
     * @return string|null
     */
    public function getRequestId(): ?string
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
     * Check if the verification was blocked
     * 
     * @return bool
     */
    public function isBlocked(): bool
    {
        return $this->_status === VerificationStatus::BLOCKED;
    }
    
    /**
     * Check if the verification should be retried
     * 
     * @return bool
     */
    public function isRetry(): bool
    {
        return $this->_status === VerificationStatus::RETRY;
    }
    
    /**
     * Check if the verification was successful
     * 
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->_status === VerificationStatus::SUCCESS;
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
     * Convert to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'channels' => $this->_channels,
            'id' => $this->_id,
            'metadata' => $this->_metadata?->toArray(),
            'method' => $this->_method->value,
            'reason' => $this->_reason?->value,
            'request_id' => $this->_requestId,
            'silent' => $this->_silent?->toArray(),
            'status' => $this->_status->value
        ];
    }
}