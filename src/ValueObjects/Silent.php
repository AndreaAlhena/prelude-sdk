<?php

namespace Prelude\SDK\ValueObjects;

/**
 * Silent verification properties
 */
class Silent
{
    private bool $enabled;
    private ?string $phoneNumber;
    
    /**
     * Create a new Silent instance
     * 
     * @param bool $enabled
     * @param string|null $phoneNumber
     */
    public function __construct(bool $enabled, ?string $phoneNumber = null)
    {
        $this->enabled = $enabled;
        $this->phoneNumber = $phoneNumber;
    }
    
    /**
     * Create from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['enabled'] ?? false,
            $data['phone_number'] ?? null
        );
    }
    
    /**
     * Get enabled status
     * 
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
    
    /**
     * Get phone number
     * 
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        $result = ['enabled' => $this->enabled];
        
        if ($this->phoneNumber !== null) {
            $result['phone_number'] = $this->phoneNumber;
        }
        
        return $result;
    }
}