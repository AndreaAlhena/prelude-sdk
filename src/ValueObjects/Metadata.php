<?php

namespace Prelude\SDK\ValueObjects;

class Metadata
{
    public function __construct(
        protected ?string $correlationId = null
    ) {
    }

    public function toArray(): array
    {
        $metadata = [];

        if ($this->correlationId !== null) {
            $metadata['correlation_id'] = $this->correlationId;
        }

        return $metadata;
    }
}