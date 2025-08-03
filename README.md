# Prelude PHP SDK

[![codecov](https://codecov.io/github/AndreaAlhena/prelude-sdk/graph/badge.svg?token=8D0QXLO08X)](https://codecov.io/github/AndreaAlhena/prelude-sdk)
[![Made with Trae](https://img.shields.io/badge/Made%20with-Trae%20AI-blueviolet?style=flat&color=32F08B)](https://trae.ai)

A comprehensive PHP SDK for integrating with Prelude services, including OTP verification.

## Installation

Install the SDK using Composer:

```bash
composer require prelude/sdk
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
// Basic lookup
$lookupResult = $client->lookup()->lookup('+1234567890');

// Lookup with additional features (e.g., CNAM)
$lookupResult = $client->lookup()->lookup('+1234567890', ['cnam']);

echo "Carrier: " . $lookupResult->getCarrier();
echo "Country: " . $lookupResult->getCountry();
echo "Line Type: " . $lookupResult->getLineType();
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