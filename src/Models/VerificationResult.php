<?php

namespace Prelude\SDK\Models;

/**
 * Verification Result model
 */
class VerificationResult
{
    private string $verificationId;
    private bool $valid;
    private string $status;
    private ?string $verifiedAt;
    private ?string $message;
    private array $rawData;
    
    /**
     * Create a new SMS verification result instance
     * 
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->rawData = $data;
        $this->verificationId = $data['verification_id'] ?? '';
        $this->valid = $data['valid'] ?? false;
        $this->status = $data['status'] ?? 'invalid';
        $this->verifiedAt = $data['verified_at'] ?? null;
        $this->message = $data['message'] ?? null;
    }
    
    /**
     * Get the verification ID
     * 
     * @return string
     */
    public function getVerificationId(): string
    {
        return $this->verificationId;
    }
    
    /**
     * Check if the verification is valid
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }
    
    /**
     * Get the verification status
     * 
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
    
    /**
     * Get the verification timestamp
     * 
     * @return string|null
     */
    public function getVerifiedAt(): ?string
    {
        return $this->verifiedAt;
    }
    
    /**
     * Get the verification message
     * 
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }
    
    /**
     * Check if the code was correct but verification failed for other reasons
     * 
     * @return bool
     */
    public function isCodeCorrect(): bool
    {
        return $this->status === 'verified' || $this->status === 'already_verified';
    }
    
    /**
     * Check if the verification was already completed
     * 
     * @return bool
     */
    public function isAlreadyVerified(): bool
    {
        return $this->status === 'already_verified';
    }
    
    /**
     * Check if the verification has expired
     * 
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }
    
    /**
     * Check if too many attempts were made
     * 
     * @return bool
     */
    public function isTooManyAttempts(): bool
    {
        return $this->status === 'too_many_attempts';
    }
    
    /**
     * Get the raw response data
     * 
     * @return array
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'verification_id' => $this->verificationId,
            'valid' => $this->valid,
            'status' => $this->status,
            'verified_at' => $this->verifiedAt,
            'message' => $this->message
        ];
    }
}