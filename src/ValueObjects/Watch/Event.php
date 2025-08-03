<?php

namespace Prelude\SDK\ValueObjects\Watch;

use Prelude\SDK\Enums\Confidence;
use Prelude\SDK\ValueObjects\Shared\Target;

class Event
{
    public function __construct(
        private Target $_target,
        private string $_label,
        private Confidence $_confidence
    ) {
        //
    }

    public function getConfidence(): Confidence
    {
        return $this->_confidence;
    }

    public function getLabel(): string
    {
        return $this->_label;
    }

    public function getTarget(): Target
    {
        return $this->_target;
    }

    public function toArray(): array
    {
        return [
            'confidence' => $this->_confidence->value,
            'label' => $this->_label,
            'target' => $this->_target->toArray(),
        ];
    }
}