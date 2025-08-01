<?php

namespace Prelude\SDK\ValueObjects;

use Prelude\SDK\Enums\OptionsMethod;
use Prelude\SDK\Enums\OptionsLocale;

class Options
{
    public function __construct(
        private string $_templateId,
        private array $_variables = [],
        private ?OptionsMethod $_method = null,
        private ?OptionsLocale $_locale = null
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

        return $result;
    }
}
