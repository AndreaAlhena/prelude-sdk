<?php

namespace Prelude\SDK\ValueObjects\Verify;

class Metadata
{
    public function __construct(
        protected ?string $_correlationId = null
    ) {
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