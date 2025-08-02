<?php

namespace Prelude\SDK\Models;

use Prelude\SDK\Enums\Flag;
use Prelude\SDK\Enums\LineType;
use Prelude\SDK\ValueObjects\Lookup\NetworkInfo;

/**
 * Represents a phone number lookup response
 */
class LookupResponse
{
    private string $_callerName;
    private string $_countryCode;
    private array $_flags;
    private LineType $_lineType;
    private NetworkInfo $_networkInfo;
    private NetworkInfo $_originalNetworkInfo;
    private string $_phoneNumber;

    public function __construct(
        string $phoneNumber,
        string $countryCode,
        NetworkInfo $networkInfo,
        NetworkInfo $originalNetworkInfo,
        array $flags,
        string $callerName,
        LineType $lineType
    ) {
        $this->_phoneNumber = $phoneNumber;
        $this->_countryCode = $countryCode;
        $this->_networkInfo = $networkInfo;
        $this->_originalNetworkInfo = $originalNetworkInfo;
        $this->_flags = $flags;
        $this->_callerName = $callerName;
        $this->_lineType = $lineType;
    }

    public static function fromArray(array $data): self
    {
        $flags = array_map(
            fn(string $flag) => Flag::from($flag),
            $data['flags'] ?? []
        );

        return new self(
            phoneNumber: $data['phone_number'],
            countryCode: $data['country_code'],
            networkInfo: NetworkInfo::fromArray($data['network_info']),
            originalNetworkInfo: NetworkInfo::fromArray($data['original_network_info']),
            flags: $flags,
            callerName: $data['caller_name'],
            lineType: LineType::from($data['line_type'])
        );
    }

    public function getCallerName(): string
    {
        return $this->_callerName;
    }

    public function getCountryCode(): string
    {
        return $this->_countryCode;
    }

    public function getFlags(): array
    {
        return $this->_flags;
    }

    public function getLineType(): LineType
    {
        return $this->_lineType;
    }

    public function getNetworkInfo(): NetworkInfo
    {
        return $this->_networkInfo;
    }

    public function getOriginalNetworkInfo(): NetworkInfo
    {
        return $this->_originalNetworkInfo;
    }

    public function getPhoneNumber(): string
    {
        return $this->_phoneNumber;
    }

    public function toArray(): array
    {
        return [
            'phone_number' => $this->_phoneNumber,
            'country_code' => $this->_countryCode,
            'network_info' => $this->_networkInfo->toArray(),
            'original_network_info' => $this->_originalNetworkInfo->toArray(),
            'flags' => array_map(fn(Flag $flag) => $flag->value, $this->_flags),
            'caller_name' => $this->_callerName,
            'line_type' => $this->_lineType->value,
        ];
    }
}