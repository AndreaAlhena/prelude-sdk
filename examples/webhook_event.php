<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DateTimeImmutable;
use PreludeSo\SDK\ValueObjects\Webhook\Event;

// Example webhook responses from Verify API
$webhookResponses = [
    [
        'id' => 'evt_01jnh4zwabf1grfsaq955ej3mt',
        'type' => 'verify.authentication',
        'created_at' => '2025-03-04T17:59:21.163921113Z',
        'payload' => [
            'metadata' => ['correlation_id' => 'e9156dad-de79-4d47-9e6b-e0c40e9244a4'],
            'price' => ['amount' => 0.009, 'currency' => 'EUR'],
            'target' => ['type' => 'phone_number', 'value' => '+33xxxxxxxx'],
            'time' => '2025-03-04T17:59:19.067887456Z',
            'verification_id' => 'vrf_01jnh4zt8vfq5r71n1sx9yvx5m'
        ]
    ],
    [
        'id' => 'evt_01jnh50110f1gt7n74yb6kcrzb',
        'type' => 'verify.attempt',
        'created_at' => '2025-03-04T17:59:25.984640901Z',
        'payload' => [
            'carrier_information' => ['mcc' => '208', 'mnc' => '10'],
            'delivery_status' => 'delivered',
            'id' => 'att_3v9s0v9gzt8hws0cp753q4gj0c',
            'metadata' => ['correlation_id' => 'e9156dad-de79-4d47-9e6b-e0c40e9244a4'],
            'price' => ['amount' => 0.03, 'currency' => 'EUR'],
            'target' => ['type' => 'phone_number', 'value' => '+33xxxxxxxx'],
            'time' => '2025-03-04T17:59:21.375073507Z',
            'verification_id' => 'vrf_01jnh4zt8vfq5r71n1sx9yvx5m'
        ]
    ],
    [
        'id' => 'evt_01jnh500a6frv9ky0wn4r6aycv',
        'type' => 'verify.delivery_status',
        'created_at' => '2025-03-04T17:59:25.254545815Z',
        'payload' => [
            'attempt_id' => 'att_3v9s0v9gzt8hws0cp753q4gj0c',
            'carrier_information' => ['mcc' => '208', 'mnc' => '10'],
            'id' => 'dls_1mzny4yepa9berf8hemgys2391',
            'metadata' => ['correlation_id' => 'e9156dad-de79-4d47-9e6b-e0c40e9244a4'],
            'price' => ['amount' => 0.03, 'currency' => 'EUR'],
            'status' => 'delivered',
            'target' => ['type' => 'phone_number', 'value' => '+33xxxxxxxx'],
            'time' => '2025-03-04T17:59:25.129596712Z',
            'verification_id' => 'vrf_01jnh4zt8vfq5r71n1sx9yvx5m'
        ]
    ]
];

echo "Webhook Event Processing Example\n";
echo "==================================\n\n";

// Process each webhook response
foreach ($webhookResponses as $index => $response) {
    // Create Event object from webhook response
    $event = new Event(
        $response['id'],
        $response['type'],
        new DateTimeImmutable($response['created_at']),
        $response['payload']
    );

    echo "Event #" . ($index + 1) . ":\n";
    echo "  ID: " . $event->getId() . "\n";
    echo "  Type: " . $event->getType() . "\n";
    echo "  Created At: " . $event->getCreatedAt()->format('Y-m-d H:i:s T') . "\n";
    echo "  Payload Keys: " . implode(', ', array_keys($event->getPayload())) . "\n";
    
    // Demonstrate accessing payload data
    $payload = $event->getPayload();
    if (isset($payload['verification_id'])) {
        echo "  Verification ID: " . $payload['verification_id'] . "\n";
    }
    if (isset($payload['target']['value'])) {
        echo "  Target: " . $payload['target']['value'] . "\n";
    }
    if (isset($payload['delivery_status'])) {
        echo "  Delivery Status: " . $payload['delivery_status'] . "\n";
    }
    echo "\n";
}

// Example of filtering events by type
echo "Filtering Events by Type:\n";
echo "========================\n\n";

$events = [];
foreach ($webhookResponses as $response) {
    $events[] = new Event(
        $response['id'],
        $response['type'],
        new DateTimeImmutable($response['created_at']),
        $response['payload']
    );
}

// Filter verification events
$verificationEvents = array_filter($events, function (Event $event) {
    return str_starts_with($event->getType(), 'verify.');
});

echo "Found " . count($verificationEvents) . " verification events:\n";
foreach ($verificationEvents as $event) {
    echo "  - " . $event->getType() . " (" . $event->getId() . ")\n";
}