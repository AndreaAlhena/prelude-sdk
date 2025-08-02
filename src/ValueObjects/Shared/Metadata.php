<?php

namespace Prelude\SDK\ValueObjects\Shared;

class Metadata
{
    public function __construct(
        private ?string $_correlationId = null
    ) {
        //
    }

    public function getCorrelationId(): ?string
    {
        return $this->_correlationId;
    }

    public function toArray(): array
    {
        $metadata = [];

        if ($this->_correlationId !== null) {
            $metadata['correlation_id'] = $this->_correlationId;
        }

        return $metadata;
    }

    public static function fromArray(array $data): self
    {
        return new self($data['correlation_id'] ?? null);
    }
}