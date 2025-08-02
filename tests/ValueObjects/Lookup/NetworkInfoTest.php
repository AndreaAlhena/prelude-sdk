<?php

use Prelude\SDK\ValueObjects\Lookup\NetworkInfo;

test('NetworkInfo → it can be instantiated with all parameters', function () {
    $networkInfo = new NetworkInfo(
        carrierName: 'SFR',
        mcc: '208',
        mnc: '13'
    );
    
    expect($networkInfo->getCarrierName())->toBe('SFR');
    expect($networkInfo->getMcc())->toBe('208');
    expect($networkInfo->getMnc())->toBe('13');
});

test('NetworkInfo → it can be created from array', function () {
    $data = [
        'carrier_name' => 'Orange',
        'mcc' => '208',
        'mnc' => '01'
    ];
    
    $networkInfo = NetworkInfo::fromArray($data);
    
    expect($networkInfo->getCarrierName())->toBe('Orange');
    expect($networkInfo->getMcc())->toBe('208');
    expect($networkInfo->getMnc())->toBe('01');
});

test('NetworkInfo → it converts to correct array format', function () {
    $networkInfo = new NetworkInfo(
        carrierName: 'Bouygues Telecom',
        mcc: '208',
        mnc: '20'
    );
    
    $array = $networkInfo->toArray();
    
    expect($array)->toBe([
        'carrier_name' => 'Bouygues Telecom',
        'mcc' => '208',
        'mnc' => '20'
    ]);
});

test('NetworkInfo → it handles special characters in carrier name', function () {
    $networkInfo = new NetworkInfo(
        carrierName: 'T-Mobile España',
        mcc: '214',
        mnc: '07'
    );
    
    expect($networkInfo->getCarrierName())->toBe('T-Mobile España');
    expect($networkInfo->toArray()['carrier_name'])->toBe('T-Mobile España');
});

test('NetworkInfo → it handles numeric strings correctly', function () {
    $networkInfo = new NetworkInfo(
        carrierName: 'Vodafone',
        mcc: '001',
        mnc: '001'
    );
    
    expect($networkInfo->getMcc())->toBe('001');
    expect($networkInfo->getMnc())->toBe('001');
    expect($networkInfo->toArray()['mcc'])->toBe('001');
    expect($networkInfo->toArray()['mnc'])->toBe('001');
});

test('NetworkInfo → it maintains immutability', function () {
    $data = [
        'carrier_name' => 'Original Carrier',
        'mcc' => '208',
        'mnc' => '13'
    ];
    
    $networkInfo = NetworkInfo::fromArray($data);
    
    // Modify original data
    $data['carrier_name'] = 'Modified Carrier';
    
    // NetworkInfo should remain unchanged
    expect($networkInfo->getCarrierName())->toBe('Original Carrier');
});