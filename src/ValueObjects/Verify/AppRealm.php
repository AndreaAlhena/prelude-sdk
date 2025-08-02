<?php

namespace Prelude\SDK\ValueObjects\Verify;

use Prelude\SDK\Enums\AppRealmPlatform;

final class AppRealm
{
    public function __construct(
        private AppRealmPlatform $_platform,
        private string $_value,
    ) {
        //
    }

    public function toArray(): array
    {
        return [
            'platform' => $this->_platform->value,
            'value' => $this->_value,
        ];
    }
}
