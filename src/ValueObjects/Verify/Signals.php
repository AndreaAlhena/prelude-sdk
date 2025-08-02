<?php

namespace Prelude\SDK\ValueObjects\Verify;

use Prelude\SDK\Enums\SignalDevicePlatform;

class Signals
{
    public function __construct(
        protected ?string $ip = null,
        protected ?string $deviceId = null,
        protected ?SignalDevicePlatform $devicePlatform = null,
        protected ?string $deviceModel = null,
        protected ?string $osVersion = null,
        protected ?string $appVersion = null,
        protected ?string $userAgent = null,
        protected ?bool $isTrustedUser = null
    ) {
    }

    public function toArray(): array
    {
        $signals = [];

        if ($this->ip !== null) {
            $signals['ip'] = $this->ip;
        }

        if ($this->deviceId !== null) {
            $signals['device_id'] = $this->deviceId;
        }

        if ($this->devicePlatform !== null) {
            $signals['device_platform'] = $this->devicePlatform->value;
        }

        if ($this->deviceModel !== null) {
            $signals['device_model'] = $this->deviceModel;
        }

        if ($this->osVersion !== null) {
            $signals['os_version'] = $this->osVersion;
        }

        if ($this->appVersion !== null) {
            $signals['app_version'] = $this->appVersion;
        }

        if ($this->userAgent !== null) {
            $signals['user_agent'] = $this->userAgent;
        }

        if ($this->isTrustedUser !== null) {
            $signals['is_trusted_user'] = $this->isTrustedUser;
        }

        return $signals;
    }
}
