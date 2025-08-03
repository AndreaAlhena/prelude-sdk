<?php

namespace Prelude\SDK\ValueObjects\Shared;

use Prelude\SDK\Enums\TargetType;

class Target
{
    public function __construct(
        private string $_value,
        private TargetType $_type
    ) {
        //
    }

    public function getValue(): string
    {
        return $this->_value;
    }

    public function getType(): TargetType
    {
        return $this->_type;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->_type->value,
            'value' => $this->_value,
        ];
    }
}