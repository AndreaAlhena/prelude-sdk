<?php

use PreludeSo\SDK\Enums\Flag;
use PreludeSo\SDK\Enums\LineType;
use PreludeSo\SDK\Models\LookupResponse;
use PreludeSo\SDK\ValueObjects\Lookup\NetworkInfo;

test('LookupResponse → it can be instantiated with all parameters', function () {
    $networkInfo = new NetworkInfo('SFR', '208', '13');
    $originalNetworkInfo = new NetworkInfo('Orange', '208', '01');
    $flags = [Flag::PORTED];
    
    $response = new LookupResponse(
        _phoneNumber: '+33123456789',
        _countryCode: 'FR',
        _networkInfo: $networkInfo,
        _originalNetworkInfo: $originalNetworkInfo,
        _flags: $flags,
        _callerName: 'FINN',
        _lineType: LineType::MOBILE
    );
    
    expect($response->getPhoneNumber())->toBe('+33123456789');
    expect($response->getCountryCode())->toBe('FR');
    expect($response->getNetworkInfo())->toBe($networkInfo);
    expect($response->getOriginalNetworkInfo())->toBe($originalNetworkInfo);
    expect($response->getFlags())->toBe($flags);
    expect($response->getCallerName())->toBe('FINN');
    expect($response->getLineType())->toBe(LineType::MOBILE);
});

test('LookupResponse → it can be created from array', function () {
    $data = [
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
    
    $response = LookupResponse::fromArray($data);
    
    expect($response->getPhoneNumber())->toBe('+33123456789');
    expect($response->getCountryCode())->toBe('FR');
    expect($response->getNetworkInfo()->getCarrierName())->toBe('SFR');
    expect($response->getOriginalNetworkInfo()->getCarrierName())->toBe('Orange');
    expect($response->getFlags())->toHaveCount(1);
    expect($response->getFlags()[0])->toBe(Flag::PORTED);
    expect($response->getCallerName())->toBe('FINN');
    expect($response->getLineType())->toBe(LineType::MOBILE);
});

test('LookupResponse → it converts to correct array format', function () {
    $networkInfo = new NetworkInfo('SFR', '208', '13');
    $originalNetworkInfo = new NetworkInfo('Orange', '208', '01');
    $flags = [Flag::PORTED, Flag::TEMPORARY];
    
    $response = new LookupResponse(
        _phoneNumber: '+33123456789',
        _countryCode: 'FR',
        _networkInfo: $networkInfo,
        _originalNetworkInfo: $originalNetworkInfo,
        _flags: $flags,
        _callerName: 'FINN',
        _lineType: LineType::MOBILE
    );
    
    $array = $response->toArray();
    
    expect($array)->toBe([
        'caller_name' => 'FINN',
        'country_code' => 'FR',
        'flags' => ['ported', 'temporary'],
        'line_type' => 'mobile',
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
        'phone_number' => '+33123456789'
    ]);
});

test('LookupResponse → it handles empty flags array', function () {
    $data = [
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
    
    $response = LookupResponse::fromArray($data);
    
    expect($response->getFlags())->toBeEmpty();
    expect($response->toArray()['flags'])->toBeEmpty();
});

test('LookupResponse → it handles different line types', function () {
    $networkInfo = new NetworkInfo('SFR', '208', '13');
    $originalNetworkInfo = new NetworkInfo('Orange', '208', '01');
    
    $response = new LookupResponse(
        _phoneNumber: '+33123456789',
        _countryCode: 'FR',
        _networkInfo: $networkInfo,
        _originalNetworkInfo: $originalNetworkInfo,
        _flags: [],
        _callerName: 'FINN',
        _lineType: LineType::FIXED_LINE
    );
    
    expect($response->getLineType())->toBe(LineType::FIXED_LINE);
    expect($response->toArray()['line_type'])->toBe('fixed_line');
});