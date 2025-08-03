<?php

/**
 * Example: Watch Service with Prelude SDK
 * 
 * This example demonstrates the Watch Service functionality.
 * Note: This is a placeholder example as the Watch Service
 * implementation may vary based on your specific SDK version.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Prelude\SDK\PreludeClient;
use Prelude\SDK\Exceptions\PreludeException;
use Prelude\SDK\Exceptions\ApiException;

// Configuration
$apiKey = getenv('PRELUDE_API_KEY') ?: 'your-api-key-here';

if ($apiKey === 'your-api-key-here') {
    echo "Please set your PRELUDE_API_KEY environment variable or update the \$apiKey variable.\n";
    exit(1);
}

// Initialize the Prelude client
$client = new PreludeClient($apiKey);

try {
    echo "=== Prelude Watch Service Example ===\n\n";
    
    // Check if Watch Service is available
    $watchService = $client->watch();
    
    if ($watchService) {
        echo "✓ Watch Service is available and ready to use.\n";
        echo "\nAvailable Watch Service methods:\n";
        echo "- predictOutcome(): Predict verification outcomes\n";
        echo "- sendFeedback(): Send feedback about verifications\n";
        echo "- dispatchEvents(): Dispatch events for monitoring\n\n";
        
        echo "For detailed usage examples, please refer to the SDK documentation.\n";
    } else {
        echo "✗ Watch Service is not available in this SDK version.\n";
    }
    
    echo "\n=== Watch Service Example Completed! ===\n";
    
} catch (ApiException $e) {
    echo "API Error: {$e->getMessage()}\n";
    echo "Error Code: {$e->getCode()}\n";
} catch (PreludeException $e) {
    echo "Prelude SDK Error: {$e->getMessage()}\n";
} catch (Exception $e) {
    echo "Unexpected Error: {$e->getMessage()}\n";
}