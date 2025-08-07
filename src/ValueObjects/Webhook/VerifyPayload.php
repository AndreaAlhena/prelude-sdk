<?php

namespace PreludeSo\SDK\ValueObjects\Webhook;

use DateTimeImmutable;

class VerifyPayload extends AbstractEventPayload
{
    private ?string $_attemptId;
    private ?array $_carrierInformation;
    private ?string $_correlationId;
    private ?string $_deliveryStatus;
    private ?Price $_price;
    private ?string $_status;
    private ?string $_target;
    private ?DateTimeImmutable $_time;
    private ?string $_verificationId;

    protected function parsePayload(array $payload): void
    {
        $this->validatePayload($payload);
        
        $this->_attemptId = $this->getValue($payload, 'attempt_id');
        $this->_carrierInformation = $this->getValue($payload, 'carrier_information');
        $this->_correlationId = $this->getNestedValue($payload, ['metadata', 'correlation_id']) 
            ?? $this->getValue($payload, 'correlation_id');
        $this->_deliveryStatus = $this->getValue($payload, 'delivery_status');
        $this->_price = $this->_parsePrice($payload);
        $this->_status = $this->getValue($payload, 'status');
        $this->_target = $this->getNestedValue($payload, ['target', 'value']) 
            ?? $this->getValue($payload, 'target');
        $this->_time = $this->_parseTime($payload);
        $this->_verificationId = $this->getValue($payload, 'verification_id');
    }

    public function getAttemptId(): ?string
    {
        return $this->_attemptId;
    }

    public function getCarrierInformation(): ?array
    {
        return $this->_carrierInformation;
    }

    public function getCorrelationId(): ?string
    {
        return $this->_correlationId;
    }

    public function getDeliveryStatus(): ?string
    {
        return $this->_deliveryStatus;
    }

    public function getPrice(): ?Price
    {
        return $this->_price;
    }

    public function getStatus(): ?string
    {
        return $this->_status ?? $this->_deliveryStatus;
    }

    public function getTarget(): ?string
    {
        return $this->_target;
    }

    public function getTime(): ?DateTimeImmutable
    {
        return $this->_time;
    }

    public function getVerificationId(): ?string
    {
        return $this->_verificationId;
    }

    protected function validatePayload(array $payload): void
    {
        // Require at least verification_id for a valid verify payload
        if (!isset($payload['verification_id']) || empty($payload['verification_id'])) {
            throw new \InvalidArgumentException('Verification ID is required for verify payload');
        }
    }

    public function toArray(): array
    {
        return [
            'attempt_id' => $this->_attemptId,
            'carrier_information' => $this->_carrierInformation,
            'correlation_id' => $this->_correlationId,
            'delivery_status' => $this->_deliveryStatus,
            'price' => $this->_price?->toArray(),
            'status' => $this->_status,
            'target' => $this->_target,
            'time' => $this->_time?->format('c'),
            'verification_id' => $this->_verificationId,
        ];
    }

    private function _parsePrice(array $payload): ?Price
    {
        $priceData = $this->getValue($payload, 'price');
        if (!$priceData || !isset($priceData['amount'], $priceData['currency'])) {
            return null;
        }

        return new Price(
            (float) $priceData['amount'],
            (string) $priceData['currency']
        );
    }

    private function _parseTime(array $payload): ?DateTimeImmutable
    {
        $timeString = $this->getValue($payload, 'time');
        if (!$timeString) {
            return null;
        }

        try {
            return new DateTimeImmutable($timeString);
        } catch (\Exception) {
            return null;
        }
    }
}
