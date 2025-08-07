<?php

namespace PreludeSo\SDK\ValueObjects\Webhook;

class Price
{
    private float $_amount;
    private string $_currency;

    public function __construct(float $amount, string $currency)
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount must be non-negative');
        }
        
        if (empty($currency)) {
            throw new \InvalidArgumentException('Currency cannot be empty');
        }
        
        $this->_amount = $amount;
        $this->_currency = strtoupper($currency);
    }

    public function getAmount(): float
    {
        return $this->_amount;
    }

    public function getCurrency(): string
    {
        return $this->_currency;
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->_amount,
            'currency' => $this->_currency,
        ];
    }

    public function __toString(): string
    {
        // Format amount to remove unnecessary decimal places
        $formattedAmount = $this->_amount == (int) $this->_amount 
            ? (string) (int) $this->_amount 
            : rtrim(rtrim(number_format($this->_amount, 10, '.', ''), '0'), '.');
            
        return $formattedAmount . ' ' . $this->_currency;
    }
}
