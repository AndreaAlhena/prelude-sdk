<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PreludeSo\SDK\Enums\WebhookEventType;
use PreludeSo\SDK\Exceptions\PreludeException;
use PreludeSo\SDK\Services\WebhookService;
use PreludeSo\SDK\ValueObjects\Webhook\GenericPayload;
use PreludeSo\SDK\ValueObjects\Webhook\TransactionalPayload;
use PreludeSo\SDK\ValueObjects\Webhook\VerifyPayload;

echo "Prelude WebhookService Example\n";
echo "==============================\n\n";

// Initialize the WebhookService
$webhookService = new WebhookService();

// Example webhook data from Prelude (as you would receive in your webhook endpoint)
$webhookEvents = [
    // Verify API Event
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
    // Transactional API Event
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
    // Custom event type
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

echo "Processing " . count($webhookEvents) . " webhook events using WebhookService...\n\n";

// Process each webhook event using the service
foreach ($webhookEvents as $index => $webhookData) {
    echo "Event #" . ($index + 1) . ":\n";
    echo "========" . str_repeat('=', strlen((string)($index + 1))) . "\n";
    
    try {
        // Process the complete webhook using the service
        $result = $webhookService->processWebhook($webhookData);
        $event = $result['event'];
        $payload = $result['payload'];
        
        echo "Event ID: " . $event->getId() . "\n";
        echo "Event Type: " . $event->getType() . "\n";
        echo "Created At: " . $event->getCreatedAt()->format('Y-m-d H:i:s T') . "\n";
        echo "Known Event Type: " . ($webhookService->isKnownEventType($event->getType()) ? 'Yes' : 'No') . "\n";
        echo "Supported Event Type: " . ($webhookService->isEventTypeSupported($event->getType()) ? 'Yes' : 'No') . "\n";
        
        $eventTypeEnum = $webhookService->getEventTypeEnum($event->getType());
        if ($eventTypeEnum) {
            echo "Event Type Enum: " . $eventTypeEnum->name . "\n";
        }
        
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
            
        } elseif ($payload instanceof TransactionalPayload) {
            echo "\n--- Transactional Event Details ---\n";
            echo "Message ID: " . ($payload->getId() ?? 'N/A') . "\n";
            echo "To: " . ($payload->getTo() ?? 'N/A') . "\n";
            
            if ($payload->getPrice()) {
                echo "Price: " . $payload->getPrice() . "\n";
            }
            
            echo "Customer UUID: " . ($payload->getCustomerUuid() ?? 'N/A') . "\n";
            echo "Correlation ID: " . ($payload->getCorrelationId() ?? 'N/A') . "\n";
            
            if ($payload->getVariables()) {
                echo "Variables: " . json_encode($payload->getVariables()) . "\n";
            }
            
            if ($payload->getCreatedAt()) {
                echo "Created At: " . $payload->getCreatedAt()->format('Y-m-d H:i:s T') . "\n";
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
        }
        
    } catch (PreludeException $e) {
        echo "Error processing webhook: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
}

// Demonstrate service utility methods
echo "WebhookService Utility Methods:\n";
echo "==============================\n";
echo "Verify events supported: " . ($webhookService->isEventTypeSupported('verify.authentication') ? 'Yes' : 'No') . "\n";
echo "Transactional events supported: " . ($webhookService->isEventTypeSupported('transactional.message.created') ? 'Yes' : 'No') . "\n";
echo "Custom events supported: " . ($webhookService->isEventTypeSupported('custom.event.type') ? 'Yes' : 'No') . "\n";
echo "Supported type prefixes: " . implode(', ', $webhookService->getSupportedEventTypes()) . "\n";
echo "All known event types: " . implode(', ', array_map(fn($case) => $case->value, WebhookEventType::cases())) . "\n\n";

// Example webhook endpoint implementation using WebhookService
echo "Example Webhook Endpoint Implementation with WebhookService:\n";
echo "========================================================\n";
echo "\n";
echo "```php\n";
echo "// webhook_endpoint.php\n";
echo "<?php\n";
echo "\n";
echo "use PreludeSo\\SDK\\Enums\\WebhookEventType;\n";
echo "use PreludeSo\\SDK\\Exceptions\\PreludeException;\n";
echo "use PreludeSo\\SDK\\Services\\WebhookService;\n";
echo "use PreludeSo\\SDK\\ValueObjects\\Webhook\\TransactionalPayload;\n";
echo "use PreludeSo\\SDK\\ValueObjects\\Webhook\\VerifyPayload;\n";
echo "\n";
echo "// Initialize the webhook service\n";
echo "\$webhookService = new WebhookService();\n";
echo "\n";
echo "// Get webhook data from request\n";
echo "\$webhookData = json_decode(file_get_contents('php://input'), true);\n";
echo "\n";
echo "try {\n";
echo "    // Process the webhook using the service\n";
echo "    \$result = \$webhookService->processWebhook(\$webhookData);\n";
echo "    \$event = \$result['event'];\n";
echo "    \$payload = \$result['payload'];\n";
echo "    \n";
echo "    // Process based on event type using enum\n";
echo "    \$eventTypeEnum = \$webhookService->getEventTypeEnum(\$event->getType());\n";
echo "    \n";
echo "    if (\$eventTypeEnum) {\n";
echo "        switch (\$eventTypeEnum) {\n";
echo "            case WebhookEventType::VERIFY_AUTHENTICATION:\n";
echo "                // Handle verification started\n";
echo "                if (\$payload instanceof VerifyPayload) {\n";
echo "                    \$verificationId = \$payload->getVerificationId();\n";
echo "                    // Process verification...\n";
echo "                }\n";
echo "                break;\n";
echo "            case WebhookEventType::TRANSACTIONAL_MESSAGE_CREATED:\n";
echo "                // Handle message created\n";
echo "                if (\$payload instanceof TransactionalPayload) {\n";
echo "                    \$messageId = \$payload->getId();\n";
echo "                    // Process message...\n";
echo "                }\n";
echo "                break;\n";
echo "            // Handle other known event types...\n";
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
echo "    }\n";
echo "    \n";
echo "    // Respond with 200 OK\n";
echo "    http_response_code(200);\n";
echo "    echo 'OK';\n";
echo "    \n";
echo "} catch (PreludeException \$e) {\n";
echo "    // Log error and respond with 500\n";
echo "    error_log('Webhook processing error: ' . \$e->getMessage());\n";
echo "    http_response_code(500);\n";
echo "    echo 'Error';\n";
echo "}\n";
echo "```\n";
echo "\n";
echo "Benefits of using WebhookService:\n";
echo "- Centralized webhook processing logic\n";
echo "- Consistent error handling\n";
echo "- Built-in validation\n";
echo "- Easy testing and mocking\n";
echo "- Clean separation of concerns\n";
echo "- Maintains existing value object architecture\n";