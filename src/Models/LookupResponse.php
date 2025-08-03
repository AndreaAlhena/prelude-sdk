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
    public function __construct(
        private string $_phoneNumber,
        private string $_countryCode,
        private NetworkInfo $_networkInfo,
        private NetworkInfo $_originalNetworkInfo,
        /** @var Flag[] */
        private array $_flags,
        private string $_callerName,
        private LineType $_lineType
    ) {
        //
    }

    public static function fromArray(array $data): self
    {
        $flags = array_map(
            fn(string $flag) => Flag::from($flag),
            $data['flags'] ?? []
        );

        return new self(
            _phoneNumber: $data['phone_number'],
            _countryCode: $data['country_code'],
            _networkInfo: NetworkInfo::fromArray($data['network_info']),
            _originalNetworkInfo: NetworkInfo::fromArray($data['original_network_info']),
            _flags: $flags,
            _callerName: $data['caller_name'],
            _lineType: LineType::from($data['line_type'])
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
            'caller_name' => $this->_callerName,
            'country_code' => $this->_countryCode,
            'flags' => array_map(fn(Flag $flag) => $flag->value, $this->_flags),
            'line_type' => $this->_lineType->value,
            'network_info' => $this->_networkInfo->toArray(),
            'original_network_info' => $this->_originalNetworkInfo->toArray(),
            'phone_number' => $this->_phoneNumber,
        ];
    }
}