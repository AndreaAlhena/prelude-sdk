<?php

/**
 * Example: Phone Number Lookup with Prelude SDK
 * 
 * This example demonstrates how to:
 * 1. Lookup basic phone number information
 * 2. Lookup with specific data types (CNAM, network info)
 * 3. Handle lookup responses and extract information
 * 4. Work with different line types and flags
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PreludeSo\SDK\PreludeClient;
use PreludeSo\SDK\Exceptions\PreludeException;
use PreludeSo\SDK\Exceptions\ApiException;
use PreludeSo\SDK\Enums\Flag;
use PreludeSo\SDK\Enums\LineType;
use PreludeSo\SDK\Enums\LookupType;

// Configuration
$apiKey = getenv('PRELUDE_API_KEY') ?: 'your-api-key-here';
$phoneNumber = '+1234567890'; // Replace with the phone number to lookup

if ($apiKey === 'your-api-key-here') {
    echo "Please set your PRELUDE_API_KEY environment variable or update the \$apiKey variable.\n";
    exit(1);
}

// Initialize the Prelude client
$client = new PreludeClient($apiKey);

try {
    echo "=== Prelude Phone Number Lookup Example ===\n\n";
    
    // Example 1: Basic lookup
    echo "1. Basic phone number lookup for {$phoneNumber}...\n";
    
    $lookupResponse = $client->lookup()->lookup($phoneNumber);
    
    echo "   ✓ Lookup completed successfully!\n";
    echo "   Phone Number: {$lookupResponse->getPhoneNumber()}\n";
    echo "   Country Code: {$lookupResponse->getCountryCode()}\n";
    echo "   Line Type: {$lookupResponse->getLineType()->value}\n";
    echo "   Caller Name: {$lookupResponse->getCallerName()}\n";
    
    // Display network information
    $networkInfo = $lookupResponse->getNetworkInfo();
    echo "   Network Info:\n";
    echo "     - Carrier: {$networkInfo->getCarrierName()}\n";
    echo "     - MCC: {$networkInfo->getMcc()}\n";
    echo "     - MNC: {$networkInfo->getMnc()}\n";
    
    // Display original network information
    $originalNetworkInfo = $lookupResponse->getOriginalNetworkInfo();
    echo "   Original Network Info:\n";
    echo "     - Carrier: {$originalNetworkInfo->getCarrierName()}\n";
    echo "     - MCC: {$originalNetworkInfo->getMcc()}\n";
    echo "     - MNC: {$originalNetworkInfo->getMnc()}\n";
    
    // Display flags
    $flags = $lookupResponse->getFlags();
    if (!empty($flags)) {
        echo "   Flags: ";
        echo implode(', ', array_map(fn($flag) => $flag->value, $flags)) . "\n";
    } else {
        echo "   Flags: None\n";
    }
    
    echo "\n";
    
    // Example 2: Lookup with specific types
    echo "2. Lookup with CNAM (Caller Name) data type...\n";
    
    $cnamLookup = $client->lookup()->lookup($phoneNumber, [LookupType::CNAM->value]);
    
    echo "   ✓ CNAM lookup completed!\n";
    echo "   Phone Number: {$cnamLookup->getPhoneNumber()}\n";
    echo "   Caller Name: {$cnamLookup->getCallerName()}\n";
    echo "   Line Type: {$cnamLookup->getLineType()->value}\n\n";
    
    // Example 3: Lookup with multiple types
    echo "3. Lookup with multiple data types (cnam, network_info)...\n";
    
    $multiTypeLookup = $client->lookup()->lookup($phoneNumber, [LookupType::CNAM->value, 'network_info']);
    
    echo "   ✓ Multi-type lookup completed!\n";
    echo "   Phone Number: {$multiTypeLookup->getPhoneNumber()}\n";
    echo "   Country Code: {$multiTypeLookup->getCountryCode()}\n";
    echo "   Caller Name: {$multiTypeLookup->getCallerName()}\n";
    echo "   Line Type: {$multiTypeLookup->getLineType()->value}\n\n";
    
    // Example 4: Working with different line types
    echo "4. Analyzing line type information...\n";
    
    $lineType = $lookupResponse->getLineType();
    
    switch ($lineType) {
        case LineType::MOBILE:
            echo "   📱 This is a mobile phone number\n";
            break;
        case LineType::FIXED_LINE:
            echo "   🏠 This is a fixed-line phone number\n";
            break;
        case LineType::VOIP:
            echo "   💻 This is a VoIP phone number\n";
            break;
        case LineType::TOLL_FREE:
            echo "   📞 This is a toll-free phone number\n";
            break;
        default:
            echo "   ❓ Line type: {$lineType->value}\n";
            break;
    }
    
    // Example 5: Working with flags
    echo "\n5. Analyzing phone number flags...\n";
    
    $flags = $lookupResponse->getFlags();
    
    if (in_array(Flag::PORTED, $flags)) {
        echo "   🔄 This number has been ported\n";
    }
    
    if (empty($flags)) {
        echo "   ✅ No special flags detected\n";
    }
    
} catch (ApiException $e) {
    echo "❌ API Error: {$e->getMessage()}\n";
    echo "   Status Code: {$e->getCode()}\n";
    if ($e->getResponseData()) {
        echo "   Response Data: " . json_encode($e->getResponseData()) . "\n";
    }
} catch (PreludeException $e) {
    echo "❌ Prelude SDK Error: {$e->getMessage()}\n";
} catch (Exception $e) {
    echo "❌ Unexpected Error: {$e->getMessage()}\n";
}

echo "\n=== Example completed ===\n";