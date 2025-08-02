<?php

/**
 * Example: OTP Verification with Prelude SDK
 * 
 * This example demonstrates how to:
 * 1. Send an OTP to a phone number
 * 2. Verify the OTP code provided by the user
 * 3. Handle various verification scenarios
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Prelude\SDK\PreludeClient;
use Prelude\SDK\Exceptions\PreludeException;
use Prelude\SDK\Exceptions\ApiException;
use Prelude\SDK\Enums\TargetType;
use Prelude\SDK\ValueObjects\Verify\Target;
use Prelude\SDK\ValueObjects\Verify\Options;

// Configuration
$apiKey = 'your-api-key-here'; // Replace with your actual API key
$phoneNumber = '+1234567890';   // Replace with the phone number to verify

// Initialize the Prelude client
$client = new PreludeClient($apiKey);

try {
    echo "=== Prelude OTP Verification Example ===\n\n";
    
    // Step 1: Create verification
    echo "1. Creating verification for {$phoneNumber}...\n";
    
    $target = new Target($phoneNumber, TargetType::PHONE_NUMBER);
    $options = new Options('default_template');
    
    $verification = $client->verification()->create($target, null, $options);
    
    echo "   ✓ Verification created successfully!\n";
    echo "   Verification ID: {$verification->getId()}\n";
    echo "   Status: {$verification->getStatus()->value}\n";
    echo "   Method: {$verification->getMethod()->value}\n";
    echo "   Request ID: {$verification->getRequestId()}\n\n";
    
    // Step 2: Simulate user input (in real application, get this from user)
    echo "2. Please enter the OTP code sent to your phone: ";
    $otpCode = trim(fgets(STDIN));
    
    // Step 3: Check the OTP
    echo "\n3. Checking OTP code...\n";
    
    $result = $client->verification()->check($target, $otpCode);
    
    if ($result->isValid()) {
        echo "   ✓ Phone number verified successfully!\n";
        echo "   Verified at: {$result->getVerifiedAt()}\n";
    } else {
        echo "   ✗ Verification failed: {$result->getMessage()}\n";
        
        // Handle specific failure scenarios based on status
        if ($result->getStatus() === 'blocked') {
            echo "   → The verification has been blocked.\n";
        } elseif ($result->getStatus() === 'retry') {
            echo "   → Please retry the verification.\n";
        } else {
            echo "   → Invalid OTP code. Please check and try again.\n";
        }
    }
    
    // Step 4: Demonstrate additional features
    echo "\n4. Additional features demonstration:\n";
    
    // Check current verification status
    echo "   Current status: {$verification->getStatus()->value}\n";
    echo "   Method: {$verification->getMethod()->value}\n";
    
    // Show status checks using available methods
     if ($verification->isSuccess()) {
         echo "   ✓ Verification was successful!\n";
     } elseif ($verification->isBlocked()) {
         echo "   ✗ Verification is blocked.\n";
     } elseif ($verification->shouldRetry()) {
         echo "   → Verification should be retried.\n";
     }
    
} catch (ApiException $e) {
    echo "API Error: {$e->getMessage()}\n";
    echo "Status Code: {$e->getCode()}\n";
    
    if ($e->isClientError()) {
        echo "This is a client error (4xx). Please check your request.\n";
    } elseif ($e->isServerError()) {
        echo "This is a server error (5xx). Please try again later.\n";
    }
    
    // Display response data if available
    $responseData = $e->getResponseData();
    if ($responseData) {
        echo "Response data: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    }
    
} catch (PreludeException $e) {
    echo "SDK Error: {$e->getMessage()}\n";
    
} catch (Exception $e) {
    echo "Unexpected error: {$e->getMessage()}\n";
}

echo "\n=== Example completed ===\n";