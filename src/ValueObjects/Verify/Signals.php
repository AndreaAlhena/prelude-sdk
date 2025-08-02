<?php

namespace Prelude\SDK\ValueObjects\Verify;

use Prelude\SDK\Enums\SignalDevicePlatform;

class Signals
{
    public function __construct(
        protected ?string $_ip = null,
        protected ?string $_deviceId = null,
        protected ?SignalDevicePlatform $_devicePlatform = null,
        protected ?string $_deviceModel = null,
        protected ?string $_osVersion = null,
        protected ?string $_appVersion = null,
        protected ?string $_userAgent = null,
        protected ?bool $_isTrustedUser = null
    ) {
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
