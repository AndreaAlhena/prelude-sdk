<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PreludeSo\SDK\PreludeClient;
use PreludeSo\SDK\Exceptions\PreludeException;

// Initialize the Prelude client
$client = new PreludeClient('your-api-key-here');

// Get the webhook service from the client
$webhookService = $client->webhook();

// Example webhook data (this would typically come from $_POST or php://input)
$webhookData = [
    'id' => 'evt_1234567890abcdef',
    'type' => 'verify.completed',
    'created_at' => '2024-12-19T10:30:00Z',
    'payload' => [
        'verification_id' => 'ver_1234567890abcdef',
        'target' => '+1234567890',
        'price' => [
            'amount' => 50,
            'currency' => 'USD'
        ],
        'time' => 1234567890
    ]
];

try {
    echo "Processing webhook using PreludeClient...\n";
    
    // Process the webhook using the client's webhook service
    $result = $webhookService->processWebhook($webhookData);
    $event = $result['event'];
    $payload = $result['payload'];
    
    echo "Event Type: " . $event->getType() . "\n";
    echo "Created At: " . $event->getCreatedAt()->format('Y-m-d H:i:s') . "\n";
    
    if ($payload) {
        echo "Payload Type: " . get_class($payload) . "\n";
        echo "Payload Data: " . json_encode($payload->toArray(), JSON_PRETTY_PRINT) . "\n";
    }
    
    echo "\n--- Alternative: Parse webhook data directly ---\n";
    
    // Alternative: Parse webhook data directly
    $parsedEvent = $webhookService->parseWebhookData($webhookData);
    echo "Parsed Event Type: " . $parsedEvent->getType() . "\n";
    
    echo "\n--- Alternative: Parse payload only ---\n";
    
    // Alternative: Parse just the payload
    $parsedPayload = $webhookService->parseWebhookPayload($webhookData['payload'], 'verify.completed');
    if ($parsedPayload) {
        echo "Parsed Payload Type: " . get_class($parsedPayload) . "\n";
        echo "Parsed Payload Data: " . json_encode($parsedPayload->toArray(), JSON_PRETTY_PRINT) . "\n";
    }
    
} catch (PreludeException $e) {
    echo "Error processing webhook: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Unexpected error: " . $e->getMessage() . "\n";
}

echo "\nWebhook processing completed!\n";