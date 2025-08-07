<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DateTimeImmutable;
use PreludeSo\SDK\Enums\WebhookEventType;
use PreludeSo\SDK\ValueObjects\Webhook\Event;
use PreludeSo\SDK\ValueObjects\Webhook\EventPayloadFactory;
use PreludeSo\SDK\ValueObjects\Webhook\VerifyPayload;
use PreludeSo\SDK\ValueObjects\Webhook\TransactionalPayload;
use PreludeSo\SDK\ValueObjects\Webhook\GenericPayload;

echo "Prelude Webhook Processing Example\n";
echo "===================================\n\n";

// Example webhook data from Prelude (as you would receive in your webhook endpoint)
$webhookEvents = [
    // Verify API Events
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
    ],
    // Transactional API Events
    [
        'id' => 'evt_01htjex3pre54tywgzsdg1jnbn',
        'type' => 'transactional.message.created',
        'created_at' => '2024-04-03T17:08:01.36881397Z',
        'payload' => [
            'id' => 'tx_01htjet67afxhta23j7dtekneh',
            'to' => '+3361234567',
            'created_at' => '2024-04-03T17:08:01.349000489Z',
            'expires_at' => '2024-04-03T17:08:01.349000489Z',
            'customer_uuid' => '90c7e8b2-203a-4984-ba25-6cf93d8fdbac',
            'variables' => ['name' => 'John'],
            'fee' => ['amount' => '0.009', 'currency' => 'EUR'],
            'correlation_id' => '8709c4f8-78b4-4a3c-8167-e99164424d0f'
        ]
    ],
    [
        'id' => 'evt_01htjex3pre54tywgzsdg1jnbn',
        'type' => 'transactional.message.pending_delivery',
        'created_at' => '2024-04-03T17:08:01.36881397Z',
        'payload' => [
            'created_at' => '2024-04-03T17:08:01.349000489Z',
            'customer_uuid' => '90c7e8b2-203a-4984-ba25-6cf93d8fdbac',
            'id' => 'dls_01htjex3p4e54rgd63kcyrq0eg',
            'message_id' => 'tx_01htjet67afxhta23j7dtekneh',
            'price' => ['amount' => '0.03', 'currency' => 'EUR'],
            'status' => 'pending',
            'mcc' => '208',
            'mnc' => '01',
            'segment_count' => 1,
            'correlation_id' => '8709c4f8-78b4-4a3c-8167-e99164424d0f'
        ]
    ],
    [
        'id' => 'evt_01htjex3pre54tywgzsdg1jnbn',
        'type' => 'transactional.message.failed',
        'created_at' => '2024-04-03T17:08:01.36881397Z',
        'payload' => [
            'created_at' => '2024-04-03T17:08:01.349000489Z',
            'customer_uuid' => '90c7e8b2-203a-4984-ba25-6cf93d8fdbac',
            'id' => 'dls_01htjex3p4e54rgd63kcyrq0eg',
            'message_id' => 'tx_01htjet67afxhta23j7dtekneh',
            'price' => ['amount' => '0.03', 'currency' => 'EUR'],
            'mcc' => '208',
            'mnc' => '01',
            'status' => 'failed',
            'correlation_id' => '8709c4f8-78b4-4a3c-8167-e99164424d0f'
        ]
    ],
    // Custom event type (will use GenericPayload)
    [
        'id' => 'evt_custom_123',
        'type' => 'custom.event.type',
        'created_at' => '2024-04-03T17:08:01.36881397Z',
        'payload' => [
            'custom_field' => 'custom_value',
            'nested_data' => ['key' => 'value'],
            'array_data' => [1, 2, 3]
        ]
    ]
];

echo "Processing " . count($webhookEvents) . " webhook events...\n\n";

// Process each webhook event
foreach ($webhookEvents as $index => $webhookData) {
    echo "Event #" . ($index + 1) . ":\n";
    echo "========" . str_repeat('=', strlen((string)($index + 1))) . "\n";
    
    // Create Event object
    $event = new Event(
        $webhookData['id'],
        $webhookData['type'],
        new DateTimeImmutable($webhookData['created_at']),
        $webhookData['payload']
    );
    
    echo "Event ID: " . $event->getId() . "\n";
    echo "Event Type: " . $event->getType() . "\n";
    echo "Created At: " . $event->getCreatedAt()->format('Y-m-d H:i:s T') . "\n";
    echo "Known Event Type: " . ($event->isKnownEventType() ? 'Yes' : 'No') . "\n";
    
    if ($event->getEventTypeEnum()) {
        echo "Event Type Enum: " . $event->getEventTypeEnum()->name . "\n";
    }
    
    // Create typed payload using factory
    try {
        $payload = EventPayloadFactory::create($webhookData['payload'], $webhookData['type']);
        
        echo "Payload Type: " . get_class($payload) . "\n";
        
        // Handle different payload types
        if ($payload instanceof VerifyPayload) {
            echo "\n--- Verify Event Details ---\n";
            echo "Verification ID: " . ($payload->getVerificationId() ?? 'N/A') . "\n";
            echo "Target: " . ($payload->getTarget() ?? 'N/A') . "\n";
            
            if ($payload->getPrice()) {
                echo "Price: " . $payload->getPrice() . "\n";
            }
            
            if ($payload->getTime()) {
                echo "Event Time: " . $payload->getTime()->format('Y-m-d H:i:s T') . "\n";
            }
            
            echo "Correlation ID: " . ($payload->getCorrelationId() ?? 'N/A') . "\n";
            
            // Event-specific fields
            if ($payload->getAttemptId()) {
                echo "Attempt ID: " . $payload->getAttemptId() . "\n";
            }
            
            if ($payload->getDeliveryStatus()) {
                echo "Delivery Status: " . $payload->getDeliveryStatus() . "\n";
            }
            
            if ($payload->getStatus()) {
                echo "Status: " . $payload->getStatus() . "\n";
            }
            
            if ($payload->getCarrierInformation()) {
                $carrier = $payload->getCarrierInformation();
                echo "Carrier: MCC=" . ($carrier['mcc'] ?? 'N/A') . ", MNC=" . ($carrier['mnc'] ?? 'N/A') . "\n";
            }
            
        } elseif ($payload instanceof TransactionalPayload) {
            echo "\n--- Transactional Event Details ---\n";
            echo "Message ID: " . ($payload->getId() ?? 'N/A') . "\n";
            echo "To: " . ($payload->getTo() ?? 'N/A') . "\n";
            echo "Status: " . ($payload->getStatus() ?? 'N/A') . "\n";
            
            if ($payload->getPrice()) {
                echo "Price: " . $payload->getPrice() . "\n";
            }
            
            echo "Segment Count: " . $payload->getSegmentCount() . "\n";
            echo "Customer UUID: " . ($payload->getCustomerUuid() ?? 'N/A') . "\n";
            echo "Correlation ID: " . ($payload->getCorrelationId() ?? 'N/A') . "\n";
            
            if ($payload->getMessageId()) {
                echo "Original Message ID: " . $payload->getMessageId() . "\n";
            }
            
            if ($payload->getMcc() && $payload->getMnc()) {
                echo "Network: MCC=" . $payload->getMcc() . ", MNC=" . $payload->getMnc() . "\n";
            }
            
            if ($payload->getVariables()) {
                echo "Variables: " . json_encode($payload->getVariables()) . "\n";
            }
            
            if ($payload->getCreatedAt()) {
                echo "Created At: " . $payload->getCreatedAt()->format('Y-m-d H:i:s T') . "\n";
            }
            
            if ($payload->getExpiresAt()) {
                echo "Expires At: " . $payload->getExpiresAt()->format('Y-m-d H:i:s T') . "\n";
            }
            
        } elseif ($payload instanceof GenericPayload) {
            echo "\n--- Generic Event Details ---\n";
            echo "Available Fields: " . implode(', ', array_keys($payload->getData())) . "\n";
            
            // Demonstrate accessing generic data
            if ($payload->has('custom_field')) {
                echo "Custom Field: " . $payload->get('custom_field') . "\n";
            }
            
            if ($payload->has('nested_data')) {
                echo "Nested Data: " . json_encode($payload->get('nested_data')) . "\n";
            }
            
            // Demonstrate dot notation access
            if ($payload->has('nested_data.key')) {
                echo "Nested Key (dot notation): " . $payload->get('nested_data.key') . "\n";
            }
            
            // Demonstrate default values
            echo "Missing Field with Default: " . $payload->get('missing_field', 'default_value') . "\n";
        }
        
    } catch (Exception $e) {
        echo "Error processing payload: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
}

// Demonstrate event filtering and processing
echo "Event Processing Examples:\n";
echo "=========================\n\n";

// Filter events by type
$verifyEvents = array_filter($webhookEvents, function($event) {
    return str_starts_with($event['type'], 'verify.');
});

$transactionalEvents = array_filter($webhookEvents, function($event) {
    return str_starts_with($event['type'], 'transactional.');
});

echo "Found " . count($verifyEvents) . " verify events\n";
echo "Found " . count($transactionalEvents) . " transactional events\n\n";

// Demonstrate factory utility methods
echo "Factory Utility Methods:\n";
echo "=======================\n";
echo "Verify events supported: " . (EventPayloadFactory::isSupported('verify.authentication') ? 'Yes' : 'No') . "\n";
echo "Transactional events supported: " . (EventPayloadFactory::isSupported('transactional.message.created') ? 'Yes' : 'No') . "\n";
echo "Custom events supported: " . (EventPayloadFactory::isSupported('custom.event.type') ? 'Yes' : 'No') . "\n";
echo "Supported type prefixes: " . implode(', ', EventPayloadFactory::getSupportedTypes()) . "\n";
echo "All known event types: " . implode(', ', array_map(fn($case) => $case->value, WebhookEventType::cases())) . "\n\n";

// Example webhook endpoint implementation
echo "Example Webhook Endpoint Implementation:\n";
echo "======================================\n";
echo "\n";
echo "```php\n";
echo "// webhook_endpoint.php\n";
echo "<?php\n";
echo "\n";
echo "use PreludeSo\\SDK\\Enums\\WebhookEventType;\n";
echo "use PreludeSo\\SDK\\ValueObjects\\Webhook\\Event;\n";
echo "use PreludeSo\\SDK\\ValueObjects\\Webhook\\EventPayloadFactory;\n";
echo "use DateTimeImmutable;\n";
echo "\n";
echo "// Get webhook data from request\n";
echo "\$webhookData = json_decode(file_get_contents('php://input'), true);\n";
echo "\n";
echo "try {\n";
echo "    // Create Event object\n";
echo "    \$event = new Event(\n";
echo "        \$webhookData['id'],\n";
echo "        \$webhookData['type'],\n";
echo "        new DateTimeImmutable(\$webhookData['created_at']),\n";
echo "        \$webhookData['payload']\n";
echo "    );\n";
echo "    \n";
echo "    // Create typed payload\n";
echo "    \$payload = EventPayloadFactory::create(\n";
echo "        \$webhookData['payload'],\n";
echo "        \$webhookData['type']\n";
echo "    );\n";
echo "    \n";
echo "    // Process based on event type using enum\n";
echo "    \$eventTypeEnum = \$event->getEventTypeEnum();\n";
echo "    \n";
echo "    if (\$eventTypeEnum) {\n";
echo "        switch (\$eventTypeEnum) {\n";
echo "            case WebhookEventType::VERIFY_AUTHENTICATION:\n";
echo "                // Handle verification started\n";
echo "                break;\n";
echo "            case WebhookEventType::VERIFY_ATTEMPT:\n";
echo "                // Handle verification attempt\n";
echo "                break;\n";
echo "            case WebhookEventType::VERIFY_DELIVERY_STATUS:\n";
echo "                // Handle delivery status update\n";
echo "                break;\n";
echo "            case WebhookEventType::TRANSACTIONAL_MESSAGE_CREATED:\n";
echo "                // Handle message created\n";
echo "                break;\n";
echo "            case WebhookEventType::TRANSACTIONAL_MESSAGE_PENDING_DELIVERY:\n";
echo "            case WebhookEventType::TRANSACTIONAL_MESSAGE_FAILED:\n";
echo "                // Handle delivery status\n";
echo "                break;\n";
echo "        }\n";
echo "    } else {\n";
echo "        // Handle unknown event types\n";
echo "        switch (\$event->getType()) {\n";
echo "            case 'custom.event.type':\n";
echo "                // Handle custom events\n";
echo "                break;\n";
echo "            default:\n";
echo "                // Handle other unknown types\n";
echo "                break;\n";
echo "        }\n";
echo "    }
";
echo "    \n";
echo "    // Respond with 200 OK\n";
echo "    http_response_code(200);\n";
echo "    echo 'OK';\n";
echo "    \n";
echo "} catch (Exception \$e) {\n";
echo "    // Log error and respond with 500\n";
echo "    error_log('Webhook processing error: ' . \$e->getMessage());\n";
echo "    http_response_code(500);\n";
echo "    echo 'Error';\n";
echo "}\n";
echo "```\n";
echo "\n";
echo "This example demonstrates how to:\n";
echo "- Parse incoming webhook data\n";
echo "- Create Event and typed payload objects\n";
echo "- Handle different event types\n";
echo "- Respond appropriately to Prelude\n";