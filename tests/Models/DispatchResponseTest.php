<?php

use PreludeSo\SDK\Models\DispatchResponse;

it('can be instantiated with all parameters', function () {
    $response = new DispatchResponse(
        _status: 'success',
        _requestId: 'req_123456789'
    );
    
    expect($response->getStatus())->toBe('success')
        ->and($response->getRequestId())->toBe('req_123456789');
});

it('can be created from array', function () {
    $data = [
        'status' => 'success',
        'request_id' => 'req_987654321'
    ];
    
    $response = DispatchResponse::fromArray($data);
    
    expect($response)->toBeInstanceOf(DispatchResponse::class)
        ->and($response->getStatus())->toBe('success')
        ->and($response->getRequestId())->toBe('req_987654321');
});

it('converts to correct array format', function () {
    $response = new DispatchResponse(
        _status: 'pending',
        _requestId: 'req_abcdef123'
    );
    
    $array = $response->toArray();
    
    expect($array)->toBe([
        'request_id' => 'req_abcdef123',
        'status' => 'pending'
    ]);
});

it('handles different status values', function () {
    $statuses = ['success', 'pending', 'failed', 'processing'];
    
    foreach ($statuses as $status) {
        $response = new DispatchResponse(
            _status: $status,
            _requestId: 'req_test'
        );
        
        expect($response->getStatus())->toBe($status);
    }
});