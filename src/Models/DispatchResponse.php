<?php

namespace PreludeSo\SDK\Models;

class DispatchResponse
{
    public function __construct(
        private string $_status,
        private string $_requestId
    ) {
        //
    }

    public static function fromArray(array $data): self
    {
        return new self(
            _status: $data['status'],
            _requestId: $data['request_id']
        );
    }

    public function getRequestId(): string
    {
        return $this->_requestId;
    }

    public function getStatus(): string
    {
        return $this->_status;
    }

    public function toArray(): array
    {
        return [
            'request_id' => $this->_requestId,
            'status' => $this->_status,
        ];
    }
}