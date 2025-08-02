<?php

use Prelude\SDK\Enums\Flag;
use Prelude\SDK\Enums\LineType;
use Prelude\SDK\Http\HttpClient;
use Prelude\SDK\Models\LookupResponse;
use Prelude\SDK\Services\LookupService;

test('LookupService → it can lookup a phone number', function () {
    $mockHttpClient = test()->createMock(HttpClient::class);
    $mockResponse = [
        'phone_number' => '+33123456789',
        'country_code' => 'FR',
        'network_info' => [
            'carrier_name' => 'SFR',
            'mcc' => '208',
            'mnc' => '13'
        ],
        'original_network_info' => [
            'carrier_name' => 'Orange',
            'mcc' => '208',
            'mnc' => '01'
        ],
        'flags' => ['ported'],
        'caller_name' => 'FINN',
        'line_type' => 'mobile'
    ];
    
    $mockHttpClient->expects(test()->once())
        ->method('get')
        ->with('/v2/lookup/' . urlencode('+33123456789'))
        ->willReturn($mockResponse);
    
    $service = new LookupService($mockHttpClient);
    $result = $service->lookup('+33123456789');
    
    expect($result)->toBeInstanceOf(LookupResponse::class);
    expect($result->getPhoneNumber())->toBe('+33123456789');
    expect($result->getCountryCode())->toBe('FR');
    expect($result->getFlags())->toHaveCount(1);
    expect($result->getFlags()[0])->toBe(Flag::PORTED);
    expect($result->getLineType())->toBe(LineType::MOBILE);
});

test('LookupService → it can lookup with type parameter', function () {
    $mockHttpClient = test()->createMock(HttpClient::class);
    $mockResponse = [
        'phone_number' => '+33123456789',
        'country_code' => 'FR',
        'network_info' => [
            'carrier_name' => 'SFR',
            'mcc' => '208',
            'mnc' => '13'
        ],
        'original_network_info' => [
            'carrier_name' => 'Orange',
            'mcc' => '208',
            'mnc' => '01'
        ],
        'flags' => ['ported'],
        'caller_name' => 'FINN',
        'line_type' => 'mobile'
    ];
    
    $mockHttpClient->expects(test()->once())
        ->method('get')
        ->with('/v2/lookup/' . urlencode('+33123456789') . '?' . http_build_query(['type' => ['cnam']]))
        ->willReturn($mockResponse);
    
    $service = new LookupService($mockHttpClient);
    $result = $service->lookup('+33123456789', ['cnam']);
    
    expect($result)->toBeInstanceOf(LookupResponse::class);
    expect($result->getCallerName())->toBe('FINN');
});

test('LookupService → it handles phone numbers with special characters', function () {
    $mockHttpClient = test()->createMock(HttpClient::class);
    $phoneNumber = '+1 (555) 123-4567';
    $encodedPhoneNumber = urlencode($phoneNumber);
    
    $mockResponse = [
        'phone_number' => $phoneNumber,
        'country_code' => 'US',
        'network_info' => [
            'carrier_name' => 'Verizon',
            'mcc' => '310',
            'mnc' => '004'
        ],
        'original_network_info' => [
            'carrier_name' => 'Verizon',
            'mcc' => '310',
            'mnc' => '004'
        ],
        'flags' => [],
        'caller_name' => 'John Doe',
        'line_type' => 'mobile'
    ];
    
    $mockHttpClient->expects(test()->once())
        ->method('get')
        ->with('/v2/lookup/' . $encodedPhoneNumber)
        ->willReturn($mockResponse);
    
    $service = new LookupService($mockHttpClient);
    $result = $service->lookup($phoneNumber);
    
    expect($result->getPhoneNumber())->toBe($phoneNumber);
    expect($result->getCountryCode())->toBe('US');
});

test('LookupService → it handles multiple type parameters', function () {
    $mockHttpClient = test()->createMock(HttpClient::class);
    $mockResponse = [
        'phone_number' => '+33123456789',
        'country_code' => 'FR',
        'network_info' => [
            'carrier_name' => 'SFR',
            'mcc' => '208',
            'mnc' => '13'
        ],
        'original_network_info' => [
            'carrier_name' => 'Orange',
            'mcc' => '208',
            'mnc' => '01'
        ],
        'flags' => ['ported', 'temporary'],
        'caller_name' => 'FINN',
        'line_type' => 'mobile'
    ];
    
    $expectedQuery = http_build_query(['type' => ['cnam', 'fraud']]);
    
    $mockHttpClient->expects(test()->once())
        ->method('get')
        ->with('/v2/lookup/' . urlencode('+33123456789') . '?' . $expectedQuery)
        ->willReturn($mockResponse);
    
    $service = new LookupService($mockHttpClient);
    $result = $service->lookup('+33123456789', ['cnam', 'fraud']);
    
    expect($result->getFlags())->toHaveCount(2);
    expect($result->getFlags())->toContain(Flag::PORTED);
    expect($result->getFlags())->toContain(Flag::TEMPORARY);
});

test('LookupService → it handles empty type array', function () {
    $mockHttpClient = test()->createMock(HttpClient::class);
    $mockResponse = [
        'phone_number' => '+33123456789',
        'country_code' => 'FR',
        'network_info' => [
            'carrier_name' => 'SFR',
            'mcc' => '208',
            'mnc' => '13'
        ],
        'original_network_info' => [
            'carrier_name' => 'Orange',
            'mcc' => '208',
            'mnc' => '01'
        ],
        'flags' => [],
        'caller_name' => 'FINN',
        'line_type' => 'mobile'
    ];
    
    $mockHttpClient->expects(test()->once())
        ->method('get')
        ->with('/v2/lookup/' . urlencode('+33123456789'))
        ->willReturn($mockResponse);
    
    $service = new LookupService($mockHttpClient);
    $result = $service->lookup('+33123456789', []);
    
    expect($result)->toBeInstanceOf(LookupResponse::class);
});