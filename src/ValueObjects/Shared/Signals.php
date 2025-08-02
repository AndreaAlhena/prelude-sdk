<?php

namespace Prelude\SDK\ValueObjects\Shared;

use Prelude\SDK\Enums\SignalDevicePlatform;

class Signals
{
    public function __construct(
        private ?string $_ip = null,
        private ?string $_deviceId = null,
        private ?SignalDevicePlatform $_devicePlatform = null,
        private ?string $_deviceModel = null,
        private ?string $_osVersion = null,
        private ?string $_appVersion = null,
        private ?string $_userAgent = null,
        private ?bool $_isTrustedUser = null
    ) {
        //
    }

    public function getAppVersion(): ?string
    {
        return $this->_appVersion;
    }

    public function getDeviceId(): ?string
    {
        return $this->_deviceId;
    }

    public function getDeviceModel(): ?string
    {
        return $this->_deviceModel;
    }

    public function getDevicePlatform(): ?SignalDevicePlatform
    {
        return $this->_devicePlatform;
    }

    public function getIp(): ?string
    {
        return $this->_ip;
    }

    public function getIsTrustedUser(): ?bool
    {
        return $this->_isTrustedUser;
    }

    public function getOsVersion(): ?string
    {
        return $this->_osVersion;
    }

    public function getUserAgent(): ?string
    {
        return $this->_userAgent;
    }

    public function toArray(): array
    {
        $signals = [];

        if ($this->_ip !== null) {
            $signals['ip'] = $this->_ip;
        }

        if ($this->_deviceId !== null) {
            $signals['device_id'] = $this->_deviceId;
        }

        if ($this->_devicePlatform !== null) {
            $signals['device_platform'] = $this->_devicePlatform->value;
        }

        if ($this->_deviceModel !== null) {
            $signals['device_model'] = $this->_deviceModel;
        }

        if ($this->_osVersion !== null) {
            $signals['os_version'] = $this->_osVersion;
        }

        if ($this->_appVersion !== null) {
            $signals['app_version'] = $this->_appVersion;
        }

        if ($this->_userAgent !== null) {
            $signals['user_agent'] = $this->_userAgent;
        }

        if ($this->_isTrustedUser !== null) {
            $signals['is_trusted_user'] = $this->_isTrustedUser;
        }

        return $signals;
    }
}