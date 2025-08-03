<?php

namespace PreludeSo\SDK\ValueObjects\Lookup;

/**
 * Represents network information for a phone number
 */
class NetworkInfo
{
    private string $_carrierName;
    private string $_mcc;
    private string $_mnc;

    public function __construct(
        string $carrierName,
        string $mcc,
        string $mnc
    ) {
        $this->_carrierName = $carrierName;
        $this->_mcc = $mcc;
        $this->_mnc = $mnc;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            carrierName: $data['carrier_name'],
            mcc: $data['mcc'],
            mnc: $data['mnc']
        );
    }

    public function getCarrierName(): string
    {
        return $this->_carrierName;
    }

    public function getMcc(): string
    {
        return $this->_mcc;
    }

    public function getMnc(): string
    {
        return $this->_mnc;
    }

    public function toArray(): array
    {
        return [
            'carrier_name' => $this->_carrierName,
            'mcc' => $this->_mcc,
            'mnc' => $this->_mnc,
        ];
    }
}