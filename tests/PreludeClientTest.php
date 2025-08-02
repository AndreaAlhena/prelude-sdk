<?php

use Prelude\SDK\PreludeClient;
use Prelude\SDK\Services\VerificationService;
use Prelude\SDK\Enums\TargetType;
use Prelude\SDK\Exceptions\PreludeException;

beforeEach(function () {
    $this->apiKey = 'test-api-key';
    $this->baseUrl = 'https://api.test.prelude.com';
});

it('can instantiate client with custom base URL', function () {
    $client = new PreludeClient($this->apiKey, $this->baseUrl);
    
    expect($client)
        ->toBeInstanceOf(PreludeClient::class)
        ->and($client->getApiKey())->toBe($this->apiKey)
        ->and($client->getBaseUrl())->toBe($this->baseUrl);
});

it('can instantiate client with default base URL', function () {
    $client = new PreludeClient($this->apiKey);
    
    expect($client)
        ->toBeInstanceOf(PreludeClient::class)
        ->and($client->getApiKey())->toBe($this->apiKey)
        ->and($client->getBaseUrl())->toBe('https://api.prelude.com');
});

it('throws exception when API key is empty', function () {
    new PreludeClient('');
})->throws(PreludeException::class, 'API key is required');

it('provides access to Verification service', function () {
    $client = new PreludeClient($this->apiKey);
    $verificationService = $client->verification();
    
    expect($verificationService)->toBeInstanceOf(VerificationService::class);
    
    // Test that the same instance is returned on subsequent calls
    $verificationService2 = $client->verification();
    expect($verificationService2)->toBe($verificationService);
});

it('has create and check methods that align with other Prelude SDKs', function () {
    $client = new PreludeClient($this->apiKey);
    $verificationService = $client->verification();
    
    // Test that the new methods exist
    expect(method_exists($verificationService, 'create'))->toBeTrue();
    expect(method_exists($verificationService, 'check'))->toBeTrue();
});

it('check method accepts API-compliant parameters', function () {
    $client = new PreludeClient($this->apiKey);
    $verificationService = $client->verification();
    
    // Test method signature with reflection
    $reflection = new ReflectionMethod($verificationService, 'check');
    $parameters = $reflection->getParameters();
    
    // Should have 2 parameters: target, code
    expect(count($parameters))->toBe(2);
    expect($parameters[0]->getName())->toBe('target');
    expect($parameters[1]->getName())->toBe('code');
    
    // Target parameter should not be nullable
    expect($parameters[0]->getType()->allowsNull())->toBeFalse();
    
    // Code parameter should not be nullable
    expect($parameters[1]->getType()->allowsNull())->toBeFalse();
});

it('create method accepts API-compliant parameters', function () {
    $client = new PreludeClient($this->apiKey);
    $verificationService = $client->verification();
    
    // Test method signature with reflection
    $reflection = new ReflectionMethod($verificationService, 'create');
    $parameters = $reflection->getParameters();
    
    // Should have 5 parameters: target, signals, options, metadata, dispatchId
    expect(count($parameters))->toBe(5);
    expect($parameters[0]->getName())->toBe('target');
    expect($parameters[1]->getName())->toBe('signals');
    expect($parameters[2]->getName())->toBe('options');
    expect($parameters[3]->getName())->toBe('metadata');
    expect($parameters[4]->getName())->toBe('dispatchId');
    
    // Signals parameter should be nullable
    expect($parameters[1]->getType()->allowsNull())->toBeTrue();
    
    // Options parameter should be nullable
    expect($parameters[2]->getType()->allowsNull())->toBeTrue();
    
    // Metadata parameter should be nullable
    expect($parameters[3]->getType()->allowsNull())->toBeTrue();
    
    // DispatchId parameter should not be nullable (has empty string default)
    expect($parameters[4]->getType()->allowsNull())->toBeFalse();
});

it('normalizes base URL by removing trailing slashes', function () {
    // Test that trailing slash is removed
    $client = new PreludeClient($this->apiKey, 'https://api.test.prelude.com/');
    expect($client->getBaseUrl())->toBe('https://api.test.prelude.com');
    
    // Test that multiple trailing slashes are removed
    $client2 = new PreludeClient($this->apiKey, 'https://api.test.prelude.com///');
    expect($client2->getBaseUrl())->toBe('https://api.test.prelude.com');
});