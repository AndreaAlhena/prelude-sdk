<?php

use PreludeSo\SDK\Config\Config;
use PreludeSo\SDK\Enums\SignalDevicePlatform;
use PreludeSo\SDK\Enums\TargetType;
use PreludeSo\SDK\Exceptions\ApiException;
use PreludeSo\SDK\Exceptions\PreludeException;
use PreludeSo\SDK\Http\HttpClient;
use PreludeSo\SDK\Models\PredictResponse;
use PreludeSo\SDK\Services\WatchService;
use PreludeSo\SDK\ValueObjects\Shared\Metadata;
use PreludeSo\SDK\ValueObjects\Shared\Signals;
use PreludeSo\SDK\ValueObjects\Shared\Target;
use PreludeSo\SDK\ValueObjects\Watch\Feedback;

describe('WatchService', function () {
    describe('predictOutcome method', function () {
        it('can predict outcome successfully with minimal data (200 response)', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new WatchService($httpClientMock);
            
            $target = new Target(
                _value: '+1234567890',
                _type: TargetType::PHONE_NUMBER
            );
            
            $signals = new Signals(
                _ip: '192.0.2.1'
            );
            
            $expectedRequestData = [
                'target' => [
                    'value' => '+1234567890',
                    'type' => 'phone_number'
                ],
                'signals' => [
                    'ip' => '192.0.2.1'
                ]
            ];
            
            $mockResponse = [
                'id' => 'pred_01jd1xq0cffycayqtdkdbv4d63',
                'prediction' => 'allow',
                'request_id' => 'req_01jd1xq0cffycayqtdkdbv4d64'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_WATCH_PREDICT, $expectedRequestData)
                ->willReturn($mockResponse);
            
            // Act
            $result = $service->predictOutcome($target, $signals);
            
            // Assert
            expect($result)
                ->toBeInstanceOf(PredictResponse::class);
            expect($result->getId())->toBe('pred_01jd1xq0cffycayqtdkdbv4d63');
            expect($result->getPrediction())->toBe('allow');
            expect($result->getRequestId())->toBe('req_01jd1xq0cffycayqtdkdbv4d64');
        });
        
        it('can predict outcome with full data including dispatch ID and metadata (200 response)', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new WatchService($httpClientMock);
            
            $target = new Target(
                _value: '+30123456789',
                _type: TargetType::PHONE_NUMBER
            );
            
            $signals = new Signals(
                _ip: '192.0.2.1',
                _deviceId: '8F0B8FDD-C2CB-4387-B20A-56E9B2E5A0D2',
                _devicePlatform: SignalDevicePlatform::IOS,
                _deviceModel: 'iPhone17,2',
                _osVersion: '18.0.1',
                _appVersion: '1.2.34',
                _userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15',
                _isTrustedUser: false
            );
            
            $dispatchId = '123e4567-e89b-12d3-a456-426614174000';
            $metadata = new Metadata(
                _correlationId: 'user-session-abc123'
            );
            
            $expectedRequestData = [
                'target' => [
                    'value' => '+30123456789',
                    'type' => 'phone_number'
                ],
                'signals' => [
                    'ip' => '192.0.2.1',
                    'device_id' => '8F0B8FDD-C2CB-4387-B20A-56E9B2E5A0D2',
                    'device_platform' => 'ios',
                    'device_model' => 'iPhone17,2',
                    'os_version' => '18.0.1',
                    'app_version' => '1.2.34',
                    'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15',
                    'is_trusted_user' => false
                ],
                'dispatch_id' => '123e4567-e89b-12d3-a456-426614174000',
                'metadata' => [
                    'correlation_id' => 'user-session-abc123'
                ]
            ];
            
            $mockResponse = [
                'id' => 'pred_01jd1xq0cffycayqtdkdbv4d65',
                'prediction' => 'block',
                'request_id' => 'req_01jd1xq0cffycayqtdkdbv4d66'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_WATCH_PREDICT, $expectedRequestData)
                ->willReturn($mockResponse);
            
            // Act
            $result = $service->predictOutcome($target, $signals, $dispatchId, $metadata);
            
            // Assert
            expect($result)
                ->toBeInstanceOf(PredictResponse::class);
            expect($result->getId())->toBe('pred_01jd1xq0cffycayqtdkdbv4d65');
            expect($result->getPrediction())->toBe('block');
            expect($result->getRequestId())->toBe('req_01jd1xq0cffycayqtdkdbv4d66');
        });
        
        it('excludes null dispatch ID from request', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new WatchService($httpClientMock);
            
            $target = new Target(
                _value: '+1234567890',
                _type: TargetType::PHONE_NUMBER
            );
            
            $signals = new Signals(
                _ip: '192.0.2.1'
            );
            
            $expectedRequestData = [
                'target' => [
                    'value' => '+1234567890',
                    'type' => 'phone_number'
                ],
                'signals' => [
                    'ip' => '192.0.2.1'
                ]
            ];
            
            $mockResponse = [
                'id' => 'pred_test',
                'prediction' => 'allow',
                'request_id' => 'req_test'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_WATCH_PREDICT, $expectedRequestData)
                ->willReturn($mockResponse);
            
            // Act
            $service->predictOutcome($target, $signals, null);
            
            // Assert - verified by mock expectations
        });
        
        it('excludes empty metadata from request', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new WatchService($httpClientMock);
            
            $target = new Target(
                _value: '+1234567890',
                _type: TargetType::PHONE_NUMBER
            );
            
            $signals = new Signals(
                _ip: '192.0.2.1'
            );
            
            $emptyMetadata = new Metadata(); // No correlation ID
            
            $expectedRequestData = [
                'target' => [
                    'value' => '+1234567890',
                    'type' => 'phone_number'
                ],
                'signals' => [
                    'ip' => '192.0.2.1'
                ]
            ];
            
            $mockResponse = [
                'id' => 'pred_test',
                'prediction' => 'allow',
                'request_id' => 'req_test'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_WATCH_PREDICT, $expectedRequestData)
                ->willReturn($mockResponse);
            
            // Act
            $service->predictOutcome($target, $signals, null, $emptyMetadata);
            
            // Assert - verified by mock expectations
        });
        
        it('handles API exceptions properly (400 response)', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new WatchService($httpClientMock);
            
            $target = new Target(
                _value: 'invalid-phone',
                _type: TargetType::PHONE_NUMBER
            );
            
            $signals = new Signals(
                _ip: '192.0.2.1'
            );
            
            $apiException = new ApiException(
                'The provided phone number is invalid. Provide a valid E.164 phone number.',
                400,
                null,
                [
                    'code' => 'invalid_phone_number',
                    'message' => 'The provided phone number is invalid. Provide a valid E.164 phone number.',
                    'type' => 'bad_request',
                    'request_id' => '3d19215e-2991-4a05-a41a-527314e6ff6a'
                ]
            );
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_WATCH_PREDICT, test()->anything())
                ->willThrowException($apiException);
            
            // Act & Assert
            try {
                $service->predictOutcome($target, $signals);
                expect(false)->toBeTrue('Expected ApiException to be thrown');
            } catch (ApiException $e) {
                expect($e->getMessage())->toBe('The provided phone number is invalid. Provide a valid E.164 phone number.');
                expect($e->getCode())->toBe(400);
                expect($e->getResponseData())->toBe([
                    'code' => 'invalid_phone_number',
                    'message' => 'The provided phone number is invalid. Provide a valid E.164 phone number.',
                    'type' => 'bad_request',
                    'request_id' => '3d19215e-2991-4a05-a41a-527314e6ff6a'
                ]);
                expect($e->isClientError())->toBeTrue();
            }
        });
        
        it('handles general exceptions properly', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new WatchService($httpClientMock);
            
            $target = new Target(
                _value: '+1234567890',
                _type: TargetType::PHONE_NUMBER
            );
            
            $signals = new Signals(
                _ip: '192.0.2.1'
            );
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_WATCH_PREDICT, test()->anything())
                ->willThrowException(new PreludeException('Network error'));
            
            // Act & Assert
            expect(fn() => $service->predictOutcome($target, $signals))
                ->toThrow(PreludeException::class, 'Network error');
        });
        
        it('handles invalid authorization token API error properly', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new WatchService($httpClientMock);
            
            $target = new Target(
                _value: '+1234567890',
                _type: TargetType::PHONE_NUMBER
            );
            
            $signals = new Signals(
                _ip: '192.0.2.1'
            );
            
            $apiException = new ApiException(
                'Invalid authorization token provided.',
                400,
                null,
                [
                    'code' => 'invalid_token',
                    'message' => 'Invalid authorization token provided.',
                    'type' => 'bad_request',
                    'request_id' => '3d19215e-2991-4a05-a41a-527314e6ff6a'
                ]
            );
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_WATCH_PREDICT, test()->anything())
                ->willThrowException($apiException);
            
            // Act & Assert
            try {
                $service->predictOutcome($target, $signals);
                expect(false)->toBeTrue('Expected ApiException to be thrown');
            } catch (ApiException $e) {
                expect($e->getMessage())->toBe('Invalid authorization token provided.');
                expect($e->getCode())->toBe(400);
                expect($e->getResponseData())->toBe([
                    'code' => 'invalid_token',
                    'message' => 'Invalid authorization token provided.',
                    'type' => 'bad_request',
                    'request_id' => '3d19215e-2991-4a05-a41a-527314e6ff6a'
                ]);
                expect($e->isClientError())->toBeTrue();
            }
        });
        
        it('sends correct endpoint to HTTP client', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new WatchService($httpClientMock);
            
            $target = new Target(
                _value: '+1234567890',
                _type: TargetType::PHONE_NUMBER
            );
            
            $signals = new Signals(
                _ip: '192.0.2.1'
            );
            
            $mockResponse = [
                'id' => 'pred_endpoint_test',
                'prediction' => 'allow',
                'request_id' => 'req_endpoint_test'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with('/v2/watch/predict', test()->anything())
                ->willReturn($mockResponse);
            
            // Act
            $service->predictOutcome($target, $signals);
            
            // Assert - verified by mock expectations
        });
        
        it('handles allow prediction outcome', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new WatchService($httpClientMock);
            
            $target = new Target(
                _value: '+1234567890',
                _type: TargetType::PHONE_NUMBER
            );
            
            $signals = new Signals(
                _ip: '192.0.2.1'
            );
            
            $mockResponse = [
                'id' => 'pred_allow_test',
                'prediction' => 'allow',
                'request_id' => 'req_allow_test'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->willReturn($mockResponse);
            
            // Act
            $result = $service->predictOutcome($target, $signals);
            
            // Assert
            expect($result->getPrediction())->toBe('allow');
        });
        
        it('handles block prediction outcome', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new WatchService($httpClientMock);
            
            $target = new Target(
                _value: '+1234567890',
                _type: TargetType::PHONE_NUMBER
            );
            
            $signals = new Signals(
                _ip: '192.0.2.1'
            );
            
            $mockResponse = [
                'id' => 'pred_block_test',
                'prediction' => 'block',
                'request_id' => 'req_block_test'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->willReturn($mockResponse);
            
            // Act
            $result = $service->predictOutcome($target, $signals);
            
            // Assert
            expect($result->getPrediction())->toBe('block');
        });
        
        it('handles review prediction outcome', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new WatchService($httpClientMock);
            
            $target = new Target(
                _value: '+1234567890',
                _type: TargetType::PHONE_NUMBER
            );
            
            $signals = new Signals(
                _ip: '192.0.2.1'
            );
            
            $mockResponse = [
                'id' => 'pred_review_test',
                'prediction' => 'review',
                'request_id' => 'req_review_test'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->willReturn($mockResponse);
            
            // Act
            $result = $service->predictOutcome($target, $signals);
            
            // Assert
            expect($result->getPrediction())->toBe('review');
        });
    });
    
    describe('constructor', function () {
        it('can be instantiated with HttpClient', function () {
            // Arrange
            $httpClient = test()->createMock(HttpClient::class);
            
            // Act
            $service = new WatchService($httpClient);
            
            // Assert
            expect($service)->toBeInstanceOf(WatchService::class);
        });
    });

    describe('sendFeedback', function () {
        it('can send feedback successfully (200 response)', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new WatchService($httpClientMock);

            $target = new Target('+30123456789', TargetType::PHONE_NUMBER);
            $signals = new Signals(
                '192.0.2.1',
                '8F0B8FDD-C2CB-4387-B20A-56E9B2E5A0D2',
                SignalDevicePlatform::IOS,
                'iPhone17,2',
                '18.0.1',
                '1.2.34',
                'Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Mobile/15E148 Safari/604.1',
                false
            );
            $metadata = new Metadata('test-correlation-id');
            $dispatchId = '123e4567-e89b-12d3-a456-426614174000';

            $feedback = new Feedback(
                $target,
                'verification.started',
                $signals,
                $dispatchId,
                $metadata
            );

            $expectedRequestData = [
                'feedbacks' => [
                    [
                        'target' => [
                            'type' => 'phone_number',
                            'value' => '+30123456789'
                        ],
                        'type' => 'verification.started',
                        'signals' => [
                            'ip' => '192.0.2.1',
                            'device_id' => '8F0B8FDD-C2CB-4387-B20A-56E9B2E5A0D2',
                            'device_platform' => 'ios',
                            'device_model' => 'iPhone17,2',
                            'os_version' => '18.0.1',
                            'app_version' => '1.2.34',
                            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Mobile/15E148 Safari/604.1',
                            'is_trusted_user' => false
                        ],
                        'dispatch_id' => '123e4567-e89b-12d3-a456-426614174000',
                        'metadata' => [
                            'correlation_id' => 'test-correlation-id'
                        ]
                    ]
                ]
            ];

            $expectedResponse = ['success' => true];

            // Act
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_WATCH_FEEDBACK, $expectedRequestData)
                ->willReturn($expectedResponse);

            $result = $service->sendFeedback([$feedback]);

            // Assert
            expect($result)->toBe($expectedResponse);
        });

        it('can send multiple feedbacks', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new WatchService($httpClientMock);

            $target1 = new Target('+30123456789', TargetType::PHONE_NUMBER);
            $target2 = new Target('+30987654321', TargetType::PHONE_NUMBER);
            $signals = new Signals('192.0.2.1');

            $feedback1 = new Feedback($target1, 'verification.started', $signals);
            $feedback2 = new Feedback($target2, 'verification.completed', $signals);

            $expectedRequestData = [
                'feedbacks' => [
                    [
                        'target' => [
                            'type' => 'phone_number',
                            'value' => '+30123456789'
                        ],
                        'type' => 'verification.started',
                        'signals' => [
                            'ip' => '192.0.2.1'
                        ]
                    ],
                    [
                        'target' => [
                            'type' => 'phone_number',
                            'value' => '+30987654321'
                        ],
                        'type' => 'verification.completed',
                        'signals' => [
                            'ip' => '192.0.2.1'
                        ]
                    ]
                ]
            ];

            $expectedResponse = ['success' => true];

            // Act
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_WATCH_FEEDBACK, $expectedRequestData)
                ->willReturn($expectedResponse);

            $result = $service->sendFeedback([$feedback1, $feedback2]);

            // Assert
            expect($result)->toBe($expectedResponse);
        });

        it('can send feedback without signals', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new WatchService($httpClientMock);

            $target = new Target('+30123456789', TargetType::PHONE_NUMBER);
            $feedback = new Feedback($target, 'verification.started');

            $expectedRequestData = [
                'feedbacks' => [
                    [
                        'target' => [
                            'type' => 'phone_number',
                            'value' => '+30123456789'
                        ],
                        'type' => 'verification.started'
                    ]
                ]
            ];

            $expectedResponse = ['success' => true];

            // Act
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_WATCH_FEEDBACK, $expectedRequestData)
                ->willReturn($expectedResponse);

            $result = $service->sendFeedback([$feedback]);

            // Assert
            expect($result)->toBe($expectedResponse);
        });

        it('handles empty feedback array', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new WatchService($httpClientMock);

            $expectedRequestData = ['feedbacks' => []];
            $expectedResponse = ['success' => true];

            // Act
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_WATCH_FEEDBACK, $expectedRequestData)
                ->willReturn($expectedResponse);

            $result = $service->sendFeedback([]);

            // Assert
            expect($result)->toBe($expectedResponse);
        });
    });
});