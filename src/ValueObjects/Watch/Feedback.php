<?php

namespace Prelude\SDK\ValueObjects\Watch;

use Prelude\SDK\ValueObjects\Shared\Metadata;
use Prelude\SDK\ValueObjects\Shared\Signals;
use Prelude\SDK\ValueObjects\Shared\Target;

class Feedback
{
    public function __construct(
        private Target $_target,
        private string $_type,
        private ?Signals $_signals = null,
        private string $_dispatchId = '',
        private ?Metadata $_metadata = null
    ) {
        //
    }

    public function getDispatchId(): string
    {
        return $this->_dispatchId;
    }

    public function getMetadata(): ?Metadata
    {
        return $this->_metadata;
    }

    public function getSignals(): ?Signals
    {
        return $this->_signals;
    }

    public function getTarget(): Target
    {
        return $this->_target;
    }

    public function getType(): string
    {
        return $this->_type;
    }

    public function toArray(): array
    {
        $data = [
            'target' => $this->_target->toArray(),
            'type' => $this->_type,
        ];

        if ($this->_signals !== null) {
            $data['signals'] = $this->_signals->toArray();
        }

        if ($this->_dispatchId !== '') {
            $data['dispatch_id'] = $this->_dispatchId;
        }

        if ($this->_metadata !== null && !empty($this->_metadata->toArray())) {
            $data['metadata'] = $this->_metadata->toArray();
        }

        return $data;
    }
}