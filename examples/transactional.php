<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PreludeSo\SDK\PreludeClient;
use PreludeSo\SDK\ValueObjects\Transactional\Options;
use PreludeSo\SDK\Exceptions\ApiException;
use PreludeSo\SDK\Exceptions\PreludeException;

echo "=== Prelude Transactional API Example ===\n\n";

// Initialize the client with your API key
$apiKey = getenv('PRELUDE_API_KEY') ?: 'your-api-key-here';

if ($apiKey === 'your-api-key-here') {
    echo "Please set your PRELUDE_API_KEY environment variable or update the \$apiKey variable.\n";
    exit(1);
}

try {
    $client = new PreludeClient($apiKey);
    
    echo "1. Basic transactional message:\n";
    
    // Basic message with minimal parameters
    $phoneNumber = '+1234567890'; // Replace with a real phone number
    $templateId = 'template_01jd1xq0cffycayqtdkdbv4d61'; // Replace with your template ID
    
    $message = $client->transactional()->send($phoneNumber, $templateId);
    
    echo "   ✓ Message sent successfully!\n";
    echo "   Message ID: {$message->getId()}\n";
    echo "   To: {$message->getTo()}\n";
    echo "   Template ID: {$message->getTemplateId()}\n";
    echo "   Created at: {$message->getCreatedAt()->format('Y-m-d H:i:s')}\n";
    echo "   Expires at: {$message->getExpiresAt()->format('Y-m-d H:i:s')}\n\n";
    
    echo "2. Advanced transactional message with options:\n";
    
    // Message with all optional parameters
    $options = new Options(
        variables: ['name' => 'John Doe', 'code' => '123456'],
        from: 'YourBrand',
        locale: 'en-US',
        expiresAt: (new DateTime('+1 hour'))->format('c'),
        callbackUrl: 'https://your-app.com/webhook',
        correlationId: 'user_12345_verification'
    );
    
    $advancedMessage = $client->transactional()->send($phoneNumber, $templateId, $options);
    
    echo "   ✓ Advanced message sent successfully!\n";
    echo "   Message ID: {$advancedMessage->getId()}\n";
    echo "   To: {$advancedMessage->getTo()}\n";
    echo "   Template ID: {$advancedMessage->getTemplateId()}\n";
    echo "   From: {$advancedMessage->getFrom()}\n";
    echo "   Variables: " . json_encode($advancedMessage->getVariables()) . "\n";
    echo "   Callback URL: {$advancedMessage->getCallbackUrl()}\n";
    echo "   Correlation ID: {$advancedMessage->getCorrelationId()}\n";
    echo "   Created at: {$advancedMessage->getCreatedAt()->format('Y-m-d H:i:s')}\n";
    echo "   Expires at: {$advancedMessage->getExpiresAt()->format('Y-m-d H:i:s')}\n\n";
    
    echo "3. Message with template variables only:\n";
    
    // Message with just template variables
    $variablesOnlyOptions = new Options(
        variables: ['customer_name' => 'Jane Smith', 'order_id' => 'ORD-789']
    );
    
    $variablesMessage = $client->transactional()->send($phoneNumber, $templateId, $variablesOnlyOptions);
    
    echo "   ✓ Message with variables sent successfully!\n";
    echo "   Message ID: {$variablesMessage->getId()}\n";
    echo "   Variables: " . json_encode($variablesMessage->getVariables()) . "\n\n";
    
    echo "4. Converting message to array:\n";
    
    $messageArray = $message->toArray();
    echo "   Message as array: " . json_encode($messageArray, JSON_PRETTY_PRINT) . "\n\n";
    
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