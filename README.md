# Prelude PHP SDK

[![codecov](https://codecov.io/github/AndreaAlhena/prelude-sdk/graph/badge.svg?token=8D0QXLO08X)](https://codecov.io/github/AndreaAlhena/prelude-sdk)
[![Made with Trae](https://img.shields.io/badge/Made%20with-Trae%20AI-blueviolet?style=flat&color=32F08B)](https://trae.ai)

A comprehensive PHP SDK for integrating with [Prelude.so](https://prelude.so) services, including OTP verification.

## Installation

Install the SDK using Composer:

```bash
composer require prelude-so/sdk
```

## Requirements

- PHP 8.1 or higher
- ext-json
- GuzzleHttp 7.0+

## Table of Contents

- [Installation](#installation)
- [Requirements](#requirements)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Verification Service](#verification-service)
  - [Create Verification](#create-verification)
  - [Check OTP](#check-otp)
  - [Resend OTP](#resend-otp)
- [Lookup Service](#lookup-service)
  - [Phone Number Lookup](#phone-number-lookup)
- [Transactional Service](#transactional-service)
  - [Send Transactional Message](#send-transactional-message)
- [Watch Service](#watch-service)
  - [Predict Verification Outcome](#predict-verification-outcome)
  - [Send Feedback](#send-feedback)
  - [Dispatch Events](#dispatch-events)
- [Webhook Service](#webhook-service)
  - [Basic Usage](#basic-usage)
  - [Available Methods](#available-methods)
  - [Benefits](#benefits)
- [Webhook Events](#webhook-events)
  - [Basic Webhook Processing](#basic-webhook-processing)
  - [Verify Webhook Events](#verify-webhook-events)
  - [Transactional Webhook Events](#transactional-webhook-events)
  - [Generic Webhook Events](#generic-webhook-events)
  - [Event Type Detection](#event-type-detection)
  - [Working with Price Objects](#working-with-price-objects)
- [Examples](#examples)
- [Error Handling](#error-handling)
- [Models](#models)
- [Development](#development)
- [Contributing](#contributing)
- [License](#license)
- [Support](#support)

## Quick Start

```php
<?php

require_once 'vendor/autoload.php';

use PreludeSo\SDK\PreludeClient;
use PreludeSo\SDK\Exceptions\PreludeException;

// Initialize the client
$client = new PreludeClient('your-api-key');

try {
    // Create a verification
    $verification = $client->verification()->create('+1234567890');
    echo "Verification ID: " . $verification->getId() . "\n";
    
    // Check the OTP (user provides the code)
    $result = $client->verification()->check($verification->getId(), '123456');
    
    if ($result->isSuccess()) {
        echo "Phone number verified successfully!\n";
    } else {
        echo "Verification failed. Status: " . $result->getStatus()->value . "\n";
    }
    
    // Process webhook data (typically in your webhook endpoint)
    $webhookData = /* webhook data from request */;
    $webhookResult = $client->webhook()->processWebhook($webhookData);
    echo "Webhook event: " . $webhookResult['event']->getType() . "\n";
    
} catch (PreludeException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

## Configuration

### Basic Configuration

```php
$client = new PreludeClient('your-api-key');
```

### Custom Base URL

```php
$client = new PreludeClient('your-api-key', 'https://custom-api.prelude.com');
```

## Verification Service

The Verification service provides OTP verification functionality.

### Create Verification

```php
use PreludeSo\SDK\Enums\TargetType;

// Basic phone number verification
$verification = $client->verification()->create('+1234567890', TargetType::PHONE_NUMBER);

// Email verification
$verification = $client->verification()->create('user@example.com', TargetType::EMAIL_ADDRESS);

// With additional options
$verification = $client->verification()->create('+1234567890', TargetType::PHONE_NUMBER, [
    'options' => [
        'template' => 'custom_template',
        'expiry_minutes' => 10,
        'code_length' => 6
    ],
    'metadata' => [
        'user_id' => '12345',
        'source' => 'mobile_app'
    ]
]);

echo "Verification ID: " . $verification->getId();
echo "Status: " . $verification->getStatus();
echo "Expires at: " . $verification->getExpiresAt();
```

### Check Verification

```php
$result = $client->verification()->check($verificationId, $userProvidedCode);

if ($result->isSuccess()) {
    echo "Verification successful!";
} else {
    echo "Verification failed. Status: " . $result->getStatus()->value;
    
    // Check specific failure reasons
    if ($result->isBlocked()) {
        echo "The verification has been blocked";
    } elseif ($result->isRetry()) {
        echo "Please retry the verification";
    }
}
```

### Resend OTP

```php
$verification = $client->verification()->resendOtp($verificationId);
echo "OTP resent. New expiry: " . $verification->getExpiresAt();
```



## Lookup Service

The Lookup Service provides phone number information and validation.

### Phone Number Lookup

```php
use PreludeSo\SDK\Enums\LookupType;

// Basic lookup
$lookupResult = $client->lookup()->lookup('+1234567890');

// Lookup with additional features (e.g., CNAM)
$lookupResult = $client->lookup()->lookup('+1234567890', [LookupType::CNAM->value]);

echo "Phone Number: " . $lookupResult->getPhoneNumber();
echo "Country Code: " . $lookupResult->getCountryCode();
echo "Line Type: " . $lookupResult->getLineType()->value;
echo "Caller Name: " . $lookupResult->getCallerName();

// Display network information
$networkInfo = $lookupResult->getNetworkInfo();
echo "Carrier: " . $networkInfo->getCarrierName();
echo "MCC: " . $networkInfo->getMcc();
echo "MNC: " . $networkInfo->getMnc();
```

## Transactional Service

The Transactional Service allows sending transactional messages.

### Send Transactional Message

```php
use PreludeSo\SDK\ValueObjects\Transactional\Options;

// Basic transactional message
$message = $client->transactional()->send(
    '+1234567890',
    'template_id_here'
);

// With additional options
$options = new Options([
    'variables' => ['name' => 'John', 'code' => '123456']
]);

$message = $client->transactional()->send(
    '+1234567890',
    'template_id_here',
    $options
);

echo "Message ID: " . $message->getId();
```

## Watch Service

The Watch Service provides fraud detection and prediction capabilities.

### Predict Verification Outcome

```php
use PreludeSo\SDK\ValueObjects\Shared\Target;
use PreludeSo\SDK\ValueObjects\Shared\Signals;
use PreludeSo\SDK\ValueObjects\Shared\Metadata;

$target = new Target('+1234567890');
$signals = new Signals([
    'ip_address' => '192.168.1.1',
    'user_agent' => 'Mozilla/5.0...'
]);

$prediction = $client->watch()->predictOutcome($target, $signals);

echo "Risk Score: " . $prediction->getRiskScore();
echo "Recommendation: " . $prediction->getRecommendation();
```

### Send Feedback

```php
use PreludeSo\SDK\ValueObjects\Watch\Feedback;

$feedbacks = [
    new Feedback([
        'verification_id' => 'ver_123',
        'outcome' => 'success',
        'fraud_detected' => false
    ])
];

$client->watch()->sendFeedback($feedbacks);
```

### Dispatch Events

```php
use PreludeSo\SDK\ValueObjects\Watch\Event;

$events = [
    new Event([
        'type' => 'verification_attempt',
        'target' => '+1234567890',
        'timestamp' => time()
    ])
];

$response = $client->watch()->dispatchEvents($events);
```

## Webhook Service

The `WebhookService` provides a centralized way to process webhook events from Prelude. It offers validation, parsing, and type-safe handling of webhook data.

### Basic Usage

```php
use PreludeSo\SDK\PreludeClient;
use PreludeSo\SDK\ValueObjects\Webhook\VerifyPayload;
use PreludeSo\SDK\ValueObjects\Webhook\TransactionalPayload;

// Access WebhookService through PreludeClient
$client = new PreludeClient('your-api-key');
$webhookService = $client->webhook();

// Get webhook data from request
$webhookData = json_decode(file_get_contents('php://input'), true);

try {
    // Process the complete webhook
    $result = $webhookService->processWebhook($webhookData);
    $event = $result['event'];
    $payload = $result['payload'];
    
    // Handle different payload types
    if ($payload instanceof VerifyPayload) {
        $verificationId = $payload->getVerificationId();
        // Handle verification event...
    } elseif ($payload instanceof TransactionalPayload) {
        $messageId = $payload->getId();
        // Handle transactional event...
    }
    
} catch (PreludeException $e) {
    // Handle webhook processing errors
    error_log('Webhook error: ' . $e->getMessage());
}
```

### Available Methods

- `processWebhook(array $webhookData)` - Process complete webhook data and return Event and typed payload
- `parseWebhookData(array $webhookData)` - Parse webhook data into an Event object
- `parseWebhookPayload(array $payload, string $eventType)` - Parse payload into typed payload object
- `isEventTypeSupported(string $eventType)` - Check if event type is supported
- `isKnownEventType(string $eventType)` - Check if event type is a known enum case
- `getEventTypeEnum(string $eventType)` - Get WebhookEventType enum for event type
- `getSupportedEventTypes()` - Get all supported event type prefixes

### Benefits

- **Centralized Processing**: All webhook logic in one place
- **Type Safety**: Automatic conversion to typed payload objects
- **Validation**: Built-in validation of webhook structure
- **Error Handling**: Consistent error handling with PreludeException
- **Testing**: Easy to test and mock
- **Maintainability**: Clean separation of concerns

## Webhook Events

The SDK provides comprehensive webhook event handling with strongly-typed payload objects for processing Prelude webhook notifications.

### Basic Webhook Processing

```php
use PreludeSo\SDK\ValueObjects\Webhook\Event;
use PreludeSo\SDK\ValueObjects\Webhook\EventPayloadFactory;
use DateTimeImmutable;

// Raw webhook data from Prelude
$webhookData = [
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
];

// Create Event object
$event = new Event(
    $webhookData['id'],
    $webhookData['type'],
    new DateTimeImmutable($webhookData['created_at']),
    $webhookData['payload']
);

// Create typed payload object
$payload = EventPayloadFactory::create($webhookData['payload'], $webhookData['type']);
```

### Verify Webhook Events

Handle verification-related webhook events with the `VerifyPayload` class:

```php
use PreludeSo\SDK\ValueObjects\Webhook\VerifyPayload;

// Verify authentication event
$authPayload = new VerifyPayload([
    'metadata' => ['correlation_id' => 'e9156dad-de79-4d47-9e6b-e0c40e9244a4'],
    'price' => ['amount' => 0.009, 'currency' => 'EUR'],
    'target' => ['type' => 'phone_number', 'value' => '+33xxxxxxxx'],
    'time' => '2025-03-04T17:59:19.067887456Z',
    'verification_id' => 'vrf_01jnh4zt8vfq5r71n1sx9yvx5m'
]);

echo "Verification ID: " . $authPayload->getVerificationId();
echo "Target: " . $authPayload->getTarget();
echo "Price: " . $authPayload->getPrice()->getAmount() . " " . $authPayload->getPrice()->getCurrency();
echo "Time: " . $authPayload->getTime()->format('Y-m-d H:i:s');
echo "Correlation ID: " . $authPayload->getCorrelationId();

// Verify attempt event
$attemptPayload = new VerifyPayload([
    'carrier_information' => ['mcc' => '208', 'mnc' => '10'],
    'delivery_status' => 'delivered',
    'id' => 'att_3v9s0v9gzt8hws0cp753q4gj0c',
    'metadata' => ['correlation_id' => 'e9156dad-de79-4d47-9e6b-e0c40e9244a4'],
    'price' => ['amount' => 0.03, 'currency' => 'EUR'],
    'target' => ['type' => 'phone_number', 'value' => '+33xxxxxxxx'],
    'time' => '2025-03-04T17:59:21.375073507Z',
    'verification_id' => 'vrf_01jnh4zt8vfq5r71n1sx9yvx5m'
]);

echo "Attempt ID: " . $attemptPayload->getAttemptId();
echo "Delivery Status: " . $attemptPayload->getDeliveryStatus();
echo "Carrier Info: MCC=" . $attemptPayload->getCarrierInformation()['mcc'] . ", MNC=" . $attemptPayload->getCarrierInformation()['mnc'];

// Verify delivery status event
$deliveryPayload = new VerifyPayload([
    'attempt_id' => 'att_3v9s0v9gzt8hws0cp753q4gj0c',
    'carrier_information' => ['mcc' => '208', 'mnc' => '10'],
    'id' => 'dls_1mzny4yepa9berf8hemgys2391',
    'metadata' => ['correlation_id' => 'e9156dad-de79-4d47-9e6b-e0c40e9244a4'],
    'price' => ['amount' => 0.03, 'currency' => 'EUR'],
    'status' => 'delivered',
    'target' => ['type' => 'phone_number', 'value' => '+33xxxxxxxx'],
    'time' => '2025-03-04T17:59:25.129596712Z',
    'verification_id' => 'vrf_01jnh4zt8vfq5r71n1sx9yvx5m'
]);

echo "Status: " . $deliveryPayload->getStatus();
echo "Attempt ID: " . $deliveryPayload->getAttemptId();
```

### Transactional Webhook Events

Handle transactional message webhook events with the `TransactionalPayload` class:

```php
use PreludeSo\SDK\ValueObjects\Webhook\TransactionalPayload;

// Transactional message created event
$createdPayload = new TransactionalPayload([
    'id' => 'tx_01htjet67afxhta23j7dtekneh',
    'to' => '+3361234567',
    'created_at' => '2024-04-03T17:08:01.349000489Z',
    'expires_at' => '2024-04-03T17:08:01.349000489Z',
    'customer_uuid' => '90c7e8b2-203a-4984-ba25-6cf93d8fdbac',
    'variables' => ['name' => 'John'],
    'fee' => ['amount' => '0.009', 'currency' => 'EUR'],
    'correlation_id' => '8709c4f8-78b4-4a3c-8167-e99164424d0f'
]);

echo "Message ID: " . $createdPayload->getId();
echo "To: " . $createdPayload->getTo();
echo "Customer UUID: " . $createdPayload->getCustomerUuid();
echo "Variables: " . json_encode($createdPayload->getVariables());
echo "Fee: " . $createdPayload->getPrice()->getAmount() . " " . $createdPayload->getPrice()->getCurrency();

// Transactional message delivery status event
$deliveryPayload = new TransactionalPayload([
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
]);

echo "Status: " . $deliveryPayload->getStatus();
echo "Message ID: " . $deliveryPayload->getMessageId();
echo "Segment Count: " . $deliveryPayload->getSegmentCount();
echo "MCC: " . $deliveryPayload->getMcc();
echo "MNC: " . $deliveryPayload->getMnc();
```

### Generic Webhook Events

For unsupported event types, the SDK provides a `GenericPayload` class:

```php
use PreludeSo\SDK\ValueObjects\Webhook\GenericPayload;

$genericPayload = new GenericPayload([
    'custom_field' => 'custom_value',
    'nested' => ['data' => 'value'],
    'array_data' => [1, 2, 3]
]);

echo "Custom Field: " . $genericPayload->get('custom_field');
echo "Nested Data: " . $genericPayload->get('nested.data'); // Dot notation supported
echo "With Default: " . $genericPayload->get('missing_field', 'default_value');
echo "Has Field: " . ($genericPayload->has('custom_field') ? 'Yes' : 'No');
```

### Event Type Detection

```php
use PreludeSo\SDK\ValueObjects\Webhook\EventPayloadFactory;
use PreludeSo\SDK\Enums\WebhookEventType;

// Check if event type is supported
if (EventPayloadFactory::isSupported('verify.authentication')) {
    echo "Verify events are supported";
}

if (EventPayloadFactory::isSupported('transactional.message.created')) {
    echo "Transactional events are supported";
}

// Get all supported event type prefixes
$supportedTypes = EventPayloadFactory::getSupportedTypes();
echo "Supported types: " . implode(', ', $supportedTypes);

// Check if event type is a known enum case
$event = new Event('evt_123', 'verify.authentication', new DateTimeImmutable());
if ($event->isKnownEventType()) {
    echo "This is a known event type";
    $enumCase = $event->getEventTypeEnum();
    echo "Enum case: " . $enumCase->name; // VERIFY_AUTHENTICATION
}

// Use enum values for type-safe event handling
switch ($event->getEventTypeEnum()) {
    case WebhookEventType::VERIFY_AUTHENTICATION:
        // Handle verification started
        break;
    case WebhookEventType::VERIFY_ATTEMPT:
        // Handle verification attempt
        break;
    case WebhookEventType::TRANSACTIONAL_MESSAGE_CREATED:
        // Handle message created
        break;
    default:
        // Handle unknown types
        break;
}
```

### Working with Price Objects

```php
use PreludeSo\SDK\ValueObjects\Webhook\Price;

// Create Price object
$price = new Price(0.03, 'EUR');

echo "Amount: " . $price->getAmount();
echo "Currency: " . $price->getCurrency();
echo "Formatted: " . $price; // Uses __toString() method

// Convert to array
$priceArray = $price->toArray();
// Result: ['amount' => 0.03, 'currency' => 'EUR']
```

## Examples

The SDK includes comprehensive examples in the `examples/` directory:

### Verification Example

```bash
php examples/verification.php
```

Demonstrates:
- Creating OTP verifications
- Checking verification codes
- Handling verification responses
- Error handling scenarios

### Transactional Messaging Example

```bash
php examples/transactional.php
```

Demonstrates:
- Sending basic transactional messages
- Using message templates
- Advanced options (variables, callbacks, etc.)
- Message status tracking

### Phone Number Lookup Example

```bash
php examples/lookup.php
```

Demonstrates:
- Basic phone number lookups
- Lookup with specific data types (CNAM, network info)
- Working with different line types
- Analyzing phone number flags
- Network information extraction

### Watch Service Example

```bash
php examples/watch.php
```

Demonstrates:
- Predicting verification outcomes
- Sending feedback data
- Dispatching events
- Fraud detection capabilities

### Webhook Event Examples

Run the basic webhook event example:

```bash
php examples/webhook_event.php
```

Run the comprehensive webhook processing example:

```bash
php examples/webhook_processing.php
```

Run the WebhookService example:

```bash
php examples/webhook_service.php
```

Run the WebhookService with PreludeClient example:

```bash
php examples/webhook_client.php
```

These examples demonstrate:
- Processing webhook events from Prelude
- Working with typed payload objects (VerifyPayload, TransactionalPayload, GenericPayload)
- Handling different event types (verify, transactional, custom)
- Extracting structured data from webhook payloads
- Real-world webhook endpoint implementation
- Event filtering and processing patterns
- Using WebhookService for centralized webhook processing
- Accessing WebhookService through PreludeClient

**Note:** Set your `PRELUDE_API_KEY` environment variable before running examples:

```bash
export PRELUDE_API_KEY="your-api-key-here"
php examples/lookup.php
```

## Error Handling

The SDK provides comprehensive error handling:

```php
use PreludeSo\SDK\Exceptions\PreludeException;
use PreludeSo\SDK\Exceptions\ApiException;

try {
    $verification = $client->verification()->create('+1234567890');
} catch (ApiException $e) {
    // API-specific errors (4xx, 5xx responses)
    echo "API Error: " . $e->getMessage();
    echo "Status Code: " . $e->getCode();
    
    if ($e->isClientError()) {
        echo "Client error (4xx)";
    } elseif ($e->isServerError()) {
        echo "Server error (5xx)";
    }
    
    // Get response data if available
    $responseData = $e->getResponseData();
    if ($responseData) {
        print_r($responseData);
    }
    
} catch (PreludeException $e) {
    // General SDK errors
    echo "SDK Error: " . $e->getMessage();
}
```

## Models

### Verification

Represents a verification request:

```php
$verification = $client->verification()->create('+1234567890');

// Properties
$verification->getId();                 // string
$verification->getPhoneNumber();        // string
$verification->getStatus();             // string
$verification->getCreatedAt();          // string|null
$verification->getExpiresAt();          // string|null
$verification->getAttemptsRemaining();  // int|null

// Status checks (use VerificationStatus enum)
// $verification->getStatus() returns one of:
// - VerificationStatus::SUCCESS->value
// - VerificationStatus::BLOCKED->value  
// - VerificationStatus::RETRY->value

// Data access
$verification->toArray();               // array
$verification->getRawData();            // array
```

### VerificationResult

Represents the result of an OTP verification:

```php
$result = $client->verification()->check($verificationId, $code);

// Properties
$result->getId();                       // string
$result->getStatus();                   // VerificationStatus enum
$result->getMethod();                   // string
$result->getReason();                   // string|null
$result->getRequestId();                // string|null
$result->getMetadata();                 // array|null
$result->getChannels();                 // array|null
$result->getSilent();                   // bool|null

// Status check methods
$result->isSuccess();                   // bool - verification successful
$result->isBlocked();                   // bool - verification blocked
$result->isRetry();                     // bool - verification can be retried

// Data access
$result->toArray();                     // array
$result->getRawData();                  // array
```

### Webhook Models

#### Event

Represents a webhook event from Prelude:

```php
use PreludeSo\SDK\ValueObjects\Webhook\Event;
use DateTimeImmutable;

$event = new Event(
    'evt_01jnh4zwabf1grfsaq955ej3mt',
    'verify.authentication',
    new DateTimeImmutable('2025-03-04T17:59:21.163921113Z'),
    ['verification_id' => 'vrf_01jnh4zt8vfq5r71n1sx9yvx5m']
);

// Properties
$event->getId();                        // string - event ID
$event->getType();                      // string - event type
$event->getCreatedAt();                 // DateTimeImmutable - when event was created
$event->getPayload();                   // array - raw payload data
```

#### VerifyPayload

Strongly-typed payload for verification webhook events:

```php
use PreludeSo\SDK\ValueObjects\Webhook\VerifyPayload;

$payload = new VerifyPayload([
    'verification_id' => 'vrf_01jnh4zt8vfq5r71n1sx9yvx5m',
    'target' => ['type' => 'phone_number', 'value' => '+33xxxxxxxx'],
    'price' => ['amount' => 0.009, 'currency' => 'EUR'],
    'time' => '2025-03-04T17:59:19.067887456Z',
    'metadata' => ['correlation_id' => 'e9156dad-de79-4d47-9e6b-e0c40e9244a4']
]);

// Properties
$payload->getVerificationId();          // string|null - verification ID
$payload->getTarget();                  // string|null - target phone/email
$payload->getPrice();                   // Price|null - cost information
$payload->getTime();                    // DateTimeImmutable|null - event time
$payload->getCorrelationId();           // string|null - correlation ID
$payload->getAttemptId();               // string|null - attempt ID (for attempt events)
$payload->getDeliveryStatus();          // string|null - delivery status
$payload->getStatus();                  // string|null - verification status
$payload->getCarrierInformation();      // array|null - carrier MCC/MNC info

// Data access
$payload->toArray();                    // array - structured data
$payload->getRawPayload();              // array - original payload
```

#### TransactionalPayload

Strongly-typed payload for transactional message webhook events:

```php
use PreludeSo\SDK\ValueObjects\Webhook\TransactionalPayload;

$payload = new TransactionalPayload([
    'id' => 'tx_01htjet67afxhta23j7dtekneh',
    'to' => '+3361234567',
    'created_at' => '2024-04-03T17:08:01.349000489Z',
    'customer_uuid' => '90c7e8b2-203a-4984-ba25-6cf93d8fdbac',
    'price' => ['amount' => '0.03', 'currency' => 'EUR'],
    'status' => 'pending',
    'segment_count' => 1
]);

// Properties
$payload->getId();                      // string|null - message/delivery ID
$payload->getTo();                      // string|null - recipient phone number
$payload->getMessageId();               // string|null - original message ID
$payload->getCreatedAt();               // DateTimeImmutable|null - creation time
$payload->getExpiresAt();               // DateTimeImmutable|null - expiration time
$payload->getCustomerUuid();            // string|null - customer identifier
$payload->getPrice();                   // Price|null - cost information
$payload->getStatus();                  // string|null - message status
$payload->getSegmentCount();            // int - number of SMS segments
$payload->getMcc();                     // string|null - mobile country code
$payload->getMnc();                     // string|null - mobile network code
$payload->getVariables();               // array|null - template variables
$payload->getCorrelationId();           // string|null - correlation ID

// Data access
$payload->toArray();                    // array - structured data
$payload->getRawPayload();              // array - original payload
```

#### GenericPayload

Flexible payload for unsupported or custom webhook event types:

```php
use PreludeSo\SDK\ValueObjects\Webhook\GenericPayload;

$payload = new GenericPayload([
    'custom_field' => 'value',
    'nested' => ['data' => 'nested_value'],
    'array_data' => [1, 2, 3]
]);

// Methods
$payload->get('custom_field');          // mixed - get field value
$payload->get('nested.data');           // mixed - get nested value with dot notation
$payload->get('missing', 'default');    // mixed - get with default value
$payload->has('custom_field');          // bool - check if field exists
$payload->getData();                    // array - get all data

// Data access
$payload->toArray();                    // array - all payload data
$payload->getRawPayload();              // array - original payload
```

#### Price

Represents monetary amounts in webhook payloads:

```php
use PreludeSo\SDK\ValueObjects\Webhook\Price;

$price = new Price(0.03, 'EUR');

// Properties
$price->getAmount();                    // float - monetary amount
$price->getCurrency();                  // string - currency code (uppercase)

// Methods
$price->toArray();                      // array - ['amount' => 0.03, 'currency' => 'EUR']
$price->__toString();                   // string - formatted as "0.03 EUR"
```

#### EventPayloadFactory

Factory for creating typed payload objects:

```php
use PreludeSo\SDK\ValueObjects\Webhook\EventPayloadFactory;

// Create typed payload based on event type
$payload = EventPayloadFactory::create($rawPayload, 'verify.authentication');
// Returns VerifyPayload instance

$payload = EventPayloadFactory::create($rawPayload, 'transactional.message.created');
// Returns TransactionalPayload instance

$payload = EventPayloadFactory::create($rawPayload, 'custom.event.type');
// Returns GenericPayload instance

// Utility methods
EventPayloadFactory::isSupported('verify.authentication');     // bool - true
EventPayloadFactory::isSupported('custom.type');               // bool - false
EventPayloadFactory::getSupportedTypes();                      // array - ['verify.', 'transactional.']
```

## Development

### Running Tests

The SDK uses Pest for testing, which provides a more expressive and elegant testing experience:

```bash
# Run all tests
composer test

# Run tests with coverage
pest --coverage

# Run specific test file
pest tests/PreludeClientTest.php

# Run tests in watch mode
pest --watch
```

### Code Style

```bash
# Check code style
composer cs-check

# Fix code style
composer cs-fix
```

### Static Analysis

```bash
composer analyse
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please contact [support@prelude.com](mailto:support@prelude.com) or visit our [documentation](https://docs.prelude.com).