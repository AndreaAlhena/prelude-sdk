<?php

namespace PreludeSo\SDK\Models;

class PredictResponse
{
    public function __construct(
        private string $_id,
        private string $_prediction,
        private string $_requestId
    ) {
        //
    }

    public static function fromArray(array $data): self
    {
        return new self(
            _id: $data['id'],
            _prediction: $data['prediction'],
            _requestId: $data['request_id']
        );
    }

    public function getId(): string
    {
        return $this->_id;
    }

    public function getPrediction(): string
    {
        return $this->_prediction;
    }

    public function getRequestId(): string
    {
        return $this->_requestId;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->_id,
            'prediction' => $this->_prediction,
            'request_id' => $this->_requestId,
        ];
    }
}