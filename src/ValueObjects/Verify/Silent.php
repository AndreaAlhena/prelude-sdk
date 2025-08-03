<?php

namespace PreludeSo\SDK\ValueObjects\Verify;

/**
 * Silent verification properties
 */
class Silent
{
    private bool $_enabled;
    private ?string $_phoneNumber;
    
    /**
     * Create a new Silent instance
     * 
     * @param bool $enabled
     * @param string|null $phoneNumber
     */
    public function __construct(bool $enabled, ?string $phoneNumber = null)
    {
        $this->_enabled = $enabled;
        $this->_phoneNumber = $phoneNumber;
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
        return $this->_enabled;
    }
    
    /**
     * Get phone number
     * 
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->_phoneNumber;
    }
    
    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        $result = ['enabled' => $this->_enabled];
        
        if ($this->_phoneNumber !== null) {
            $result['phone_number'] = $this->_phoneNumber;
        }
        
        return $result;
    }
}