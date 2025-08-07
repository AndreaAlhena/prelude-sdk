<?php

namespace PreludeSo\SDK\ValueObjects\Webhook;

use DateTimeImmutable;

class TransactionalPayload extends AbstractEventPayload
{
    private ?string $_correlationId;
    private ?DateTimeImmutable $_createdAt;
    private ?string $_customerUuid;
    private ?DateTimeImmutable $_expiresAt;
    private ?string $_id;
    private ?string $_mcc;
    private ?string $_messageId;
    private ?string $_mnc;
    private ?Price $_price;
    private int $_segmentCount;
    private ?string $_status;
    private ?string $_to;
    private ?array $_variables;

    protected function parsePayload(array $payload): void
    {
        $this->_correlationId = $this->getValue($payload, 'correlation_id');
        $this->_createdAt = $this->_parseDateTime($payload, 'created_at');
        $this->_customerUuid = $this->getValue($payload, 'customer_uuid');
        $this->_expiresAt = $this->_parseDateTime($payload, 'expires_at');
        $this->_id = $this->getValue($payload, 'id');
        $this->_mcc = $this->getValue($payload, 'mcc');
        $this->_messageId = $this->getValue($payload, 'message_id');
        $this->_mnc = $this->getValue($payload, 'mnc');
        $this->_price = $this->_parsePrice($payload);
        $this->_segmentCount = $this->_parseSegmentCount($payload);
        $this->_status = $this->getValue($payload, 'status');
        $this->_to = $this->getValue($payload, 'to');
        $this->_variables = $this->getValue($payload, 'variables');
    }

    public function getCorrelationId(): ?string
    {
        return $this->_correlationId;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->_createdAt;
    }

    public function getCustomerUuid(): ?string
    {
        return $this->_customerUuid;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->_expiresAt;
    }

    public function getId(): ?string
    {
        return $this->_id;
    }

    public function getMcc(): ?string
    {
        return $this->_mcc;
    }

    public function getMessageId(): ?string
    {
        return $this->_messageId;
    }

    public function getMnc(): ?string
    {
        return $this->_mnc;
    }

    public function getPrice(): ?Price
    {
        return $this->_price;
    }

    public function getSegmentCount(): int
    {
        return $this->_segmentCount;
    }

    public function getStatus(): ?string
    {
        return $this->_status;
    }

    public function getTo(): ?string
    {
        return $this->_to;
    }

    public function getVariables(): ?array
    {
        return $this->_variables;
    }

    public function toArray(): array
    {
        return [
            'correlation_id' => $this->_correlationId,
            'created_at' => $this->_createdAt?->format('c'),
            'customer_uuid' => $this->_customerUuid,
            'expires_at' => $this->_expiresAt?->format('c'),
            'id' => $this->_id,
            'mcc' => $this->_mcc,
            'message_id' => $this->_messageId,
            'mnc' => $this->_mnc,
            'price' => $this->_price?->toArray(),
            'segment_count' => $this->_segmentCount,
            'status' => $this->_status,
            'to' => $this->_to,
            'variables' => $this->_variables,
        ];
    }

    private function _parseDateTime(array $payload, string $key): ?DateTimeImmutable
    {
        $dateString = $this->getValue($payload, $key);
        if (!$dateString) {
            return null;
        }

        try {
            return new DateTimeImmutable($dateString);
        } catch (\Exception) {
            return null;
        }
    }

    private function _parsePrice(array $payload): ?Price
    {
        // Try 'fee' first, then 'price'
        $priceData = $this->getValue($payload, 'fee') ?? $this->getValue($payload, 'price');
        if (!$priceData || !isset($priceData['amount'], $priceData['currency'])) {
            return null;
        }

        return new Price(
            (float) $priceData['amount'],
            (string) $priceData['currency']
        );
    }

    private function _parseSegmentCount(array $payload): int
    {
        $segmentCount = $this->getValue($payload, 'segment_count');
        return $segmentCount !== null ? (int) $segmentCount : 0;
    }
}
