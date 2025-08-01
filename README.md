# Prelude PHP SDK

A comprehensive PHP SDK for integrating with Prelude services, including OTP verification.

## Installation

Install the SDK using Composer:

```bash
composer require prelude/sdk
```

## Requirements

- PHP 7.4 or higher
- ext-json
- GuzzleHttp 7.0+

## Quick Start

```php
<?php

require_once 'vendor/autoload.php';

use Prelude\SDK\PreludeClient;
use Prelude\SDK\Exceptions\PreludeException;

// Initialize the client
$client = new PreludeClient('your-api-key');

try {
    // Send an OTP
    $verification = $client->verification()->sendOtp('+1234567890');
    echo "Verification ID: " . $verification->getId() . "\n";
    
    // Verify the OTP (user provides the code)
    $result = $client->verification()->verifyOtp($verification->getId(), '123456');
    
    if ($result->isValid()) {
        echo "Phone number verified successfully!\n";
    } else {
        echo "Verification failed: " . $result->getMessage() . "\n";
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

### Send OTP

```php
// Basic OTP send
$verification = $client->verification()->sendOtp('+1234567890');

// With additional options
$verification = $client->verification()->sendOtp('+1234567890', [
    'template' => 'custom_template',
    'expiry_minutes' => 10,
    'code_length' => 6
]);

echo "Verification ID: " . $verification->getId();
echo "Status: " . $verification->getStatus();
echo "Expires at: " . $verification->getExpiresAt();
```

### Verify OTP

```php
$result = $client->verification()->verifyOtp($verificationId, $userProvidedCode);

if ($result->isValid()) {
    echo "Verification successful!";
} else {
    echo "Verification failed: " . $result->getMessage();
    
    // Check specific failure reasons
    if ($result->isExpired()) {
        echo "The code has expired";
    } elseif ($result->isTooManyAttempts()) {
        echo "Too many attempts made";
    }
}
```

### Check Verification Status

```php
$verification = $client->verification()->getVerificationStatus($verificationId);

echo "Status: " . $verification->getStatus();
echo "Attempts remaining: " . $verification->getAttemptsRemaining();

// Check status with helper methods
if ($verification->isPending()) {
    echo "Verification is still pending";
} elseif ($verification->isVerified()) {
    echo "Verification completed";
} elseif ($verification->isExpired()) {
    echo "Verification expired";
}
```

### Resend OTP

```php
$verification = $client->verification()->resendOtp($verificationId);
echo "OTP resent. New expiry: " . $verification->getExpiresAt();
```

### Cancel Verification

```php
$cancelled = $client->verification()->cancelVerification($verificationId);
if ($cancelled) {
    echo "Verification cancelled successfully";
}
```

## Error Handling

The SDK provides comprehensive error handling:

```php
use Prelude\SDK\Exceptions\PreludeException;
use Prelude\SDK\Exceptions\ApiException;

try {
    $verification = $client->verification()->sendOtp('+1234567890');
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
$verification = $client->verification()->sendOtp('+1234567890');

// Properties
$verification->getId();                 // string
$verification->getPhoneNumber();        // string
$verification->getStatus();             // string
$verification->getCreatedAt();          // string|null
$verification->getExpiresAt();          // string|null
$verification->getAttemptsRemaining();  // int|null

// Status checks
$verification->isPending();             // bool
$verification->isVerified();            // bool
$verification->isExpired();             // bool
$verification->isCancelled();           // bool

// Data access
$verification->toArray();               // array
$verification->getRawData();            // array
```

### VerificationResult

Represents the result of an OTP verification:

```php
$result = $client->verification()->verifyOtp($verificationId, $code);

// Properties
$result->getVerificationId();           // string
$result->isValid();                     // bool
$result->getStatus();                   // string
$result->getVerifiedAt();               // string|null
$result->getMessage();                  // string|null

// Status checks
$result->isCodeCorrect();               // bool
$result->isAlreadyVerified();           // bool
$result->isExpired();                   // bool
$result->isTooManyAttempts();           // bool

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