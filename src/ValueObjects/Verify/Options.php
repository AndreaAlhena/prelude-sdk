<?php

namespace Prelude\SDK\ValueObjects\Verify;

use Prelude\SDK\Enums\OptionsMethod;
use Prelude\SDK\Enums\OptionsLocale;
use Prelude\SDK\Enums\PreferredChannel;
use Prelude\SDK\ValueObjects\Verify\AppRealm;

class Options
{
    public function __construct(
        private string $_templateId,
        private array $_variables = [],
        private ?OptionsMethod $_method = null,
        private ?OptionsLocale $_locale = null,
        private ?AppRealm $_appRealm = null,
        private int $_codeSize = 0,
        private string $_customCode = '',
        private string $_callbackUrl = '',
        private ?PreferredChannel $_preferredChannel = null
    ) {
        //
    }

    public function toArray(): array
    {
        $result = [
            'template_id' => $this->_templateId,
            'variables' => $this->_variables,
        ];

        if ($this->_method !== null) {
            $result['method'] = $this->_method->value;
        }

        if ($this->_locale !== null) {
            $result['locale'] = $this->_locale->value;
        }

        if ($this->_appRealm !== null) {
            $result['app_realm'] = $this->_appRealm->toArray();
        }

        if ($this->_codeSize > 0) {
            $result['code_size'] = $this->_codeSize;
        }

        if ($this->_customCode !== '') {
            $result['custom_code'] = $this->_customCode;
        }

        if ($this->_callbackUrl !== '') {
            $result['callback_url'] = $this->_callbackUrl;
        }

        if ($this->_preferredChannel !== null) {
            $result['preferred_channel'] = $this->_preferredChannel->value;
        }

        return $result;
    }
}
