<?php

namespace PreludeSo\SDK\ValueObjects\Verify;

use PreludeSo\SDK\Enums\OptionsMethod;
use PreludeSo\SDK\Enums\OptionsLocale;
use PreludeSo\SDK\Enums\PreferredChannel;
use PreludeSo\SDK\ValueObjects\Verify\AppRealm;

class Options
{
    public function __construct(
        private string $_templateId = '',
        private array $_variables = [],
        private ?AppRealm $_appRealm = null,
        private string $_callbackUrl = '',
        private int $_codeSize = 0,
        private string $_customCode = '',
        private ?OptionsLocale $_locale = null,
        private ?OptionsMethod $_method = null,
        private ?PreferredChannel $_preferredChannel = null
    ) {
        //
    }

    public function toArray(): array
    {
        $result = [];

        if ($this->_templateId !== '') {
            $result['template_id'] = $this->_templateId;
        }

        if ($this->_variables !== []) {
            $result['variables'] = $this->_variables;
        }

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
