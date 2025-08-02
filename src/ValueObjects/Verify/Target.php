<?php

namespace Prelude\SDK\ValueObjects\Verify;

use Prelude\SDK\Enums\TargetType;

class Target
{
    public function __construct(
        private string $_value,
        private TargetType $_type
    ) {
        //
    }

    public function toArray(): array
    {
        return [
            'target' => [
                'value' => $this->_value,
                'type' => $this->_type->value,
            ]
        ];
    }
}
