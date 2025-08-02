<?php

use Prelude\SDK\Config\Config;
use Prelude\SDK\Enums\Confidence;
use Prelude\SDK\Enums\TargetType;
use Prelude\SDK\Http\HttpClient;
use Prelude\SDK\Models\DispatchResponse;
use Prelude\SDK\Services\WatchService;
use Prelude\SDK\ValueObjects\Shared\Target;
use Prelude\SDK\ValueObjects\Watch\Event;

describe('WatchService dispatchEvents', function () {
    it('can dispatch single event successfully', function () {
        // Arrange
        $httpClientMock = test()->createMock(HttpClient::class);
        $service = new WatchService($httpClientMock);
        
        $target = new Target(
            _value: '+1234567890',
            _type: TargetType::PHONE_NUMBER
        );
        
        $event = new Event(
            _target: $target,
            _label: 'fraud_detected',
            _confidence: Confidence::HIGH
        );
        
        $expectedRequestData = [
            'events' => [
                [
                    'target' => [
                        'value' => '+1234567890',
                        'type' => 'phone_number'
                    ],
                    'label' => 'fraud_detected',
                    'confidence' => 'high'
                ]
            ]
        ];
        
        $mockResponse = [
            'status' => 'success',
            'request_id' => 'req_123456789'
        ];
        
        $httpClientMock
            ->expects(test()->once())
            ->method('post')
            ->with(Config::ENDPOINT_WATCH_EVENT, $expectedRequestData)
            ->willReturn($mockResponse);
        
        // Act
        $result = $service->dispatchEvents([$event]);
        
        // Assert
        expect($result)->toBeInstanceOf(DispatchResponse::class)
            ->and($result->getStatus())->toBe('success')
            ->and($result->getRequestId())->toBe('req_123456789');
    });
    
    it('can dispatch multiple events successfully', function () {
        // Arrange
        $httpClientMock = test()->createMock(HttpClient::class);
        $service = new WatchService($httpClientMock);
        
        $target1 = new Target(
            _value: '+1234567890',
            _type: TargetType::PHONE_NUMBER
        );
        
        $target2 = new Target(
            _value: 'test@example.com',
            _type: TargetType::EMAIL_ADDRESS
        );
        
        $event1 = new Event(
            _target: $target1,
            _label: 'fraud_detected',
            _confidence: Confidence::HIGH
        );
        
        $event2 = new Event(
            _target: $target2,
            _label: 'suspicious_activity',
            _confidence: Confidence::NEUTRAL
        );
        
        $expectedRequestData = [
            'events' => [
                [
                    'target' => [
                        'value' => '+1234567890',
                        'type' => 'phone_number'
                    ],
                    'label' => 'fraud_detected',
                    'confidence' => 'high'
                ],
                [
                    'target' => [
                        'value' => 'test@example.com',
                        'type' => 'email_address'
                    ],
                    'label' => 'suspicious_activity',
                    'confidence' => 'neutral'
                ]
            ]
        ];
        
        $mockResponse = [
            'status' => 'success',
            'request_id' => 'req_987654321'
        ];
        
        $httpClientMock
            ->expects(test()->once())
            ->method('post')
            ->with(Config::ENDPOINT_WATCH_EVENT, $expectedRequestData)
            ->willReturn($mockResponse);
        
        // Act
        $result = $service->dispatchEvents([$event1, $event2]);
        
        // Assert
        expect($result)->toBeInstanceOf(DispatchResponse::class)
            ->and($result->getStatus())->toBe('success')
            ->and($result->getRequestId())->toBe('req_987654321');
    });
    
    it('works with different confidence levels', function () {
        // Arrange
        $httpClientMock = test()->createMock(HttpClient::class);
        $service = new WatchService($httpClientMock);
        
        $target = new Target(
            _value: '+1234567890',
            _type: TargetType::PHONE_NUMBER
        );
        
        $event = new Event(
            _target: $target,
            _label: 'test_event',
            _confidence: Confidence::MAXIMUM
        );
        
        $expectedRequestData = [
            'events' => [
                [
                    'target' => [
                        'value' => '+1234567890',
                        'type' => 'phone_number'
                    ],
                    'label' => 'test_event',
                    'confidence' => 'maximum'
                ]
            ]
        ];
        
        $mockResponse = [
            'status' => 'success',
            'request_id' => 'req_confidence_test'
        ];
        
        $httpClientMock
            ->expects(test()->once())
            ->method('post')
            ->with(Config::ENDPOINT_WATCH_EVENT, $expectedRequestData)
            ->willReturn($mockResponse);
        
        // Act
        $result = $service->dispatchEvents([$event]);
        
        // Assert
        expect($result)->toBeInstanceOf(DispatchResponse::class)
            ->and($result->getStatus())->toBe('success');
    });
    
    it('sends correct endpoint', function () {
        // Arrange
        $httpClientMock = test()->createMock(HttpClient::class);
        $service = new WatchService($httpClientMock);
        
        $target = new Target(
            _value: '+1234567890',
            _type: TargetType::PHONE_NUMBER
        );
        
        $event = new Event(
            _target: $target,
            _label: 'test_event',
            _confidence: Confidence::LOW
        );
        
        $mockResponse = [
            'status' => 'success',
            'request_id' => 'req_endpoint_test'
        ];
        
        $httpClientMock
            ->expects(test()->once())
            ->method('post')
            ->with('/v2/watch/event', test()->anything())
            ->willReturn($mockResponse);
        
        // Act
        $service->dispatchEvents([$event]);
        
        // Assert - expectations are verified by PHPUnit mock
    });
});