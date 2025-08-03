<?php

use PreludeSo\SDK\Services\VerificationService;
use PreludeSo\SDK\Http\HttpClient;
use PreludeSo\SDK\Models\Verification;
use PreludeSo\SDK\Models\VerificationResult;
use PreludeSo\SDK\ValueObjects\Shared\Target;
use PreludeSo\SDK\Enums\TargetType;
use PreludeSo\SDK\Config\Config;
use PreludeSo\SDK\Exceptions\ApiException;

describe('VerificationService', function () {

    describe('create method', function () {
        it('can create a verification with phone number target', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new VerificationService($httpClientMock);
            
            $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
            
            $expectedRequestData = [
                'target' => [
                    'value' => '+1234567890',
                    'type' => 'phone_number'
                ]
            ];
            
            $mockResponse = [
                'id' => 'vrf_01jc0t6fwwfgfsq1md24mhyztj',
                'target' => [
                    'value' => '+1234567890',
                    'type' => 'phone_number'
                ],
                'status' => 'success',
                'expires_at' => '2024-01-15T10:30:00Z',
                'created_at' => '2024-01-15T10:00:00Z',
                'attempts' => 0,
                'max_attempts' => 3
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_VERIFICATION, $expectedRequestData)
                ->willReturn($mockResponse);
            
            // Act
            $result = $service->create($target, null, null);
            
            // Assert
            expect($result)
                ->toBeInstanceOf(Verification::class);
        });

        it('can create a verification with email target', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new VerificationService($httpClientMock);
            
            $target = new Target('test@example.com', TargetType::EMAIL_ADDRESS);
            
            $expectedRequestData = [
                'target' => [
                    'value' => 'test@example.com',
                    'type' => 'email_address'
                ]
            ];
            
            $mockResponse = [
                'id' => 'vrf_01jc0t6fwwfgfsq1md24mhyztj',
                'target' => [
                    'value' => 'test@example.com',
                    'type' => 'email_address'
                ],
                'status' => 'success',
                'expires_at' => '2024-01-15T10:30:00Z',
                'created_at' => '2024-01-15T10:00:00Z',
                'attempts' => 0,
                'max_attempts' => 3
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_VERIFICATION, $expectedRequestData)
                ->willReturn($mockResponse);
            
            // Act
            $result = $service->create($target, null, null);
            
            // Assert
            expect($result)
                ->toBeInstanceOf(Verification::class);
        });

        it('throws ApiException on invalid phone number (400 response)', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new VerificationService($httpClientMock);
            
            $target = new Target('invalid-phone', TargetType::PHONE_NUMBER);
            
            $expectedRequestData = [
                'target' => [
                    'value' => 'invalid-phone',
                    'type' => 'phone_number'
                ]
            ];
            
            // Create an ApiException with the specific error structure from the API
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
                ->with(Config::ENDPOINT_VERIFICATION, $expectedRequestData)
                ->willThrowException($apiException);
            
            // Act & Assert
            try {
                $service->create($target, null, null);
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
    });

    describe('check method', function () {
        it('can verify an OTP code successfully (200 response)', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new VerificationService($httpClientMock);
            
            $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
            $code = '123456';
            
            $expectedRequestData = [
                'code' => $code,
                'target' => [
                    'value' => '+1234567890',
                    'type' => 'phone_number'
                ]
            ];
            
            $mockResponse = [
                'id' => 'vrf_01jc0t6fwwfgfsq1md24mhyztj',
                'status' => 'success',
                'reason' => null,
                'method' => 'message',
                'channels' => [
                    'sms'
                ],
                'silent' => [
                    'request_url' => 'https://example.com/webhook'
                ],
                'metadata' => [
                    'correlation_id' => 'corr_123456'
                ],
                'request_id' => 'req_01jc0t6fwwfgfsq1md24mhyztj'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_VERIFICATION_CHECK, $expectedRequestData)
                ->willReturn($mockResponse);
            
            // Act
            $result = $service->check($target, $code);
            
            // Assert
            expect($result)
                ->toBeInstanceOf(VerificationResult::class);
        });

        it('handles failed verification with reason (200 response)', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new VerificationService($httpClientMock);
            
            $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
            $code = '000000';
            
            $expectedRequestData = [
                'code' => $code,
                'target' => [
                    'value' => '+1234567890',
                    'type' => 'phone_number'
                ]
            ];
            
            $mockResponse = [
                'id' => 'vrf_01jc0t6fwwfgfsq1md24mhyztj',
                'status' => 'blocked',
                'reason' => 'invalid_phone_number',
                'method' => 'message',
                'channels' => [
                    'sms'
                ],
                'silent' => [
                    'request_url' => 'https://example.com/webhook'
                ],
                'metadata' => [
                    'correlation_id' => 'corr_123456'
                ],
                'request_id' => 'req_01jc0t6fwwfgfsq1md24mhyztj'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_VERIFICATION_CHECK, $expectedRequestData)
                ->willReturn($mockResponse);
            
            // Act
            $result = $service->check($target, $code);
            
            // Assert
            expect($result)
                ->toBeInstanceOf(VerificationResult::class);
        });

        it('throws ApiException on invalid phone number during check (400 response)', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new VerificationService($httpClientMock);
            
            $target = new Target('invalid-phone', TargetType::PHONE_NUMBER);
            $code = '123456';
            
            $expectedRequestData = [
                'code' => $code,
                'target' => [
                    'value' => 'invalid-phone',
                    'type' => 'phone_number'
                ]
            ];
            
            // Create an ApiException with the specific error structure from the API
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
                ->with(Config::ENDPOINT_VERIFICATION_CHECK, $expectedRequestData)
                ->willThrowException($apiException);
            
            // Act & Assert
            try {
                $service->check($target, $code);
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
    });





    describe('resendOtp method', function () {
        it('can resend OTP successfully', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new VerificationService($httpClientMock);
            
            $verificationId = 'vrf_01jc0t6fwwfgfsq1md24mhyztj';
            
            $mockResponse = [
                'id' => $verificationId,
                'target' => [
                    'value' => '+1234567890',
                    'type' => 'phone_number'
                ],
                'status' => 'success',
                'expires_at' => '2024-01-15T10:30:00Z',
                'created_at' => '2024-01-15T10:00:00Z',
                'attempts' => 2,
                'max_attempts' => 3
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_VERIFICATION . '/' . $verificationId . '/resend')
                ->willReturn($mockResponse);
            
            // Act
            $result = $service->resendOtp($verificationId);
            
            // Assert
            expect($result)
                ->toBeInstanceOf(Verification::class);
        });

        it('throws ApiException when resend fails (400 response)', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new VerificationService($httpClientMock);
            
            $verificationId = 'invalid_verification_id';
            
            // Create an ApiException with the specific error structure from the API
            $apiException = new ApiException(
                'Maximum resend attempts exceeded.',
                400,
                null,
                [
                    'code' => 'max_resend_attempts_exceeded',
                    'message' => 'Maximum resend attempts exceeded.',
                    'type' => 'bad_request',
                    'request_id' => '3d19215e-2991-4a05-a41a-527314e6ff6a'
                ]
            );
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_VERIFICATION . '/' . $verificationId . '/resend')
                ->willThrowException($apiException);
            
            // Act & Assert
            try {
                $service->resendOtp($verificationId);
                expect(false)->toBeTrue('Expected ApiException to be thrown');
            } catch (ApiException $e) {
                expect($e->getMessage())->toBe('Maximum resend attempts exceeded.');
                expect($e->getCode())->toBe(400);
                expect($e->getResponseData())->toBe([
                    'code' => 'max_resend_attempts_exceeded',
                    'message' => 'Maximum resend attempts exceeded.',
                    'type' => 'bad_request',
                    'request_id' => '3d19215e-2991-4a05-a41a-527314e6ff6a'
                ]);
                expect($e->isClientError())->toBeTrue();
            }
        });
    });

    describe('authorization', function () {
        it('throws ApiException when authorization header is missing (400 response)', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new VerificationService($httpClientMock);
            
            $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
            
            $expectedRequestData = [
                'target' => [
                    'value' => '+1234567890',
                    'type' => 'phone_number'
                ]
            ];
            
            // Create an ApiException with the specific error structure from the API
            $apiException = new ApiException(
                'Authorization header is missing or invalid.',
                400,
                null,
                [
                    'code' => 'unauthorized',
                    'message' => 'Authorization header is missing or invalid.',
                    'type' => 'bad_request',
                    'request_id' => '3d19215e-2991-4a05-a41a-527314e6ff6a'
                ]
            );
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_VERIFICATION, $expectedRequestData)
                ->willThrowException($apiException);
            
            // Act & Assert
            try {
                $service->create($target, null, null);
                expect(false)->toBeTrue('Expected ApiException to be thrown');
            } catch (ApiException $e) {
                expect($e->getMessage())->toBe('Authorization header is missing or invalid.');
                expect($e->getCode())->toBe(400);
                expect($e->getResponseData())->toBe([
                    'code' => 'unauthorized',
                    'message' => 'Authorization header is missing or invalid.',
                    'type' => 'bad_request',
                    'request_id' => '3d19215e-2991-4a05-a41a-527314e6ff6a'
                ]);
                expect($e->isClientError())->toBeTrue();
            }
        });

        it('throws ApiException when authorization token is invalid (400 response)', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new VerificationService($httpClientMock);
            
            $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
            $code = '123456';
            
            $expectedRequestData = [
                'code' => $code,
                'target' => [
                    'value' => '+1234567890',
                    'type' => 'phone_number'
                ]
            ];
            
            // Create an ApiException with the specific error structure from the API
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
                ->with(Config::ENDPOINT_VERIFICATION_CHECK, $expectedRequestData)
                ->willThrowException($apiException);
            
            // Act & Assert
            try {
                $service->check($target, $code);
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
    });

    describe('endpoint validation', function () {
        it('sends correct endpoint for create method', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new VerificationService($httpClientMock);
            
            $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
            
            $mockResponse = [
                'id' => 'vrf_01jc0t6fwwfgfsq1md24mhyztj',
                'target' => [
                    'value' => '+1234567890',
                    'type' => 'phone_number'
                ],
                'status' => 'success'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with('/v2/verification', test()->anything())
                ->willReturn($mockResponse);
            
            // Act
            $service->create($target, null, null);
            
            // Assert - expectations are verified by PHPUnit mock
        });

        it('sends correct endpoint for check method', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new VerificationService($httpClientMock);
            
            $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
            $code = '123456';
            
            $mockResponse = [
                'id' => 'vrf_01jc0t6fwwfgfsq1md24mhyztj',
                'status' => 'success'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with('/v2/verification/check', test()->anything())
                ->willReturn($mockResponse);
            
            // Act
            $service->check($target, $code);
            
            // Assert - expectations are verified by PHPUnit mock
        });
    });
});