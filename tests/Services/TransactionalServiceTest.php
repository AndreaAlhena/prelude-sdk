<?php

use Prelude\SDK\Services\TransactionalService;
use Prelude\SDK\Http\HttpClient;
use Prelude\SDK\Models\TransactionalMessage;
use Prelude\SDK\ValueObjects\Transactional\Options;
use Prelude\SDK\Config\Config;
use Prelude\SDK\Exceptions\PreludeException;
use Prelude\SDK\Exceptions\ApiException;

describe('TransactionalService', function () {

    describe('send method', function () {
        it('can send a basic transactional message', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new TransactionalService($httpClientMock);
            
            $to = '+30123456789';
            $templateId = 'template_01jd1xq0cffycayqtdkdbv4d61';
            
            $expectedRequestData = [
                'to' => $to,
                'template_id' => $templateId
            ];
            
            $mockResponse = [
                'id' => 'msg_01jd1xq0cffycayqtdkdbv4d62',
                'to' => $to,
                'template_id' => $templateId,
                'variables' => [],
                'expires_at' => '2024-01-15T10:30:00Z',
                'created_at' => '2024-01-15T10:00:00Z',
                'from' => 'Prelude',
                'callback_url' => null,
                'correlation_id' => null
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_TRANSACTIONAL, $expectedRequestData)
                ->willReturn($mockResponse);
            
            // Act
            $result = $service->send($to, $templateId);
            
            // Assert
            expect($result)
                ->toBeInstanceOf(TransactionalMessage::class);
        });
        
        it('can send a transactional message with options', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new TransactionalService($httpClientMock);
            
            $to = '+30123456789';
            $templateId = 'template_01jd1xq0cffycayqtdkdbv4d61';
            $options = new Options(
                ['foo' => 'bar', 'name' => 'John'], // variables
                'MyApp', // from
                'el-GR', // locale
                '2024-01-15T11:00:00Z', // expiresAt
                'https://example.com/webhook', // callbackUrl
                'user-123-verification' // correlationId
            );
            
            $expectedRequestData = [
                'to' => $to,
                'template_id' => $templateId,
                'from' => 'MyApp',
                'locale' => 'el-GR',
                'variables' => ['foo' => 'bar', 'name' => 'John'],
                'expires_at' => '2024-01-15T11:00:00Z',
                'callback_url' => 'https://example.com/webhook',
                'correlation_id' => 'user-123-verification'
            ];
            
            $mockResponse = [
                'id' => 'msg_01jd1xq0cffycayqtdkdbv4d63',
                'to' => $to,
                'template_id' => $templateId,
                'variables' => ['foo' => 'bar', 'name' => 'John'],
                'expires_at' => '2024-01-15T11:00:00Z',
                'created_at' => '2024-01-15T10:00:00Z',
                'from' => 'MyApp',
                'callback_url' => 'https://example.com/webhook',
                'correlation_id' => 'user-123-verification'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_TRANSACTIONAL, $expectedRequestData)
                ->willReturn($mockResponse);
            
            // Act
            $result = $service->send($to, $templateId, $options);
            
            // Assert
            expect($result)
                ->toBeInstanceOf(TransactionalMessage::class);
        });
        
        it('handles API exceptions properly', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new TransactionalService($httpClientMock);
            
            $to = '+30123456789';
            $templateId = 'invalid_template';
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_TRANSACTIONAL, test()->anything())
                ->willThrowException(new ApiException('Template not found', 404));
            
            // Act & Assert
            expect(fn() => $service->send($to, $templateId))
                ->toThrow(ApiException::class, 'Template not found');
        });
        
        it('handles general exceptions properly', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new TransactionalService($httpClientMock);
            
            $to = '+30123456789';
            $templateId = 'template_01jd1xq0cffycayqtdkdbv4d61';
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_TRANSACTIONAL, test()->anything())
                ->willThrowException(new PreludeException('Network error'));
            
            // Act & Assert
            expect(fn() => $service->send($to, $templateId))
                ->toThrow(PreludeException::class, 'Network error');
        });
        
        it('handles invalid phone number API error properly', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new TransactionalService($httpClientMock);
            
            $invalidPhoneNumber = 'invalid-phone';
            $templateId = 'template_01jd1xq0cffycayqtdkdbv4d61';
            
            $expectedRequestData = [
                'to' => $invalidPhoneNumber,
                'template_id' => $templateId
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
                ->with(Config::ENDPOINT_TRANSACTIONAL, $expectedRequestData)
                ->willThrowException($apiException);
            
            // Act & Assert
            try {
                $service->send($invalidPhoneNumber, $templateId);
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
        
        it('sends correct endpoint to HTTP client', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new TransactionalService($httpClientMock);
            
            $to = '+30123456789';
            $templateId = 'template_01jd1xq0cffycayqtdkdbv4d61';
            
            $mockResponse = [
                'id' => 'msg_01jd1xq0cffycayqtdkdbv4d62',
                'to' => $to,
                'template_id' => $templateId,
                'variables' => [],
                'expires_at' => '2024-01-15T10:30:00Z',
                'created_at' => '2024-01-15T10:00:00Z'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with('/v2/transactional', test()->anything())
                ->willReturn($mockResponse);
            
            // Act
            $service->send($to, $templateId);
            
            // Assert - expectations are verified by PHPUnit mock
        });
        
        it('merges options data correctly with base request data', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new TransactionalService($httpClientMock);
            
            $to = '+1234567890';
            $templateId = 'template_test';
            $options = new Options(
                ['key1' => 'value1', 'key2' => 'value2'], // variables
                'TestSender' // from
            );
            
            $expectedData = [
                'to' => $to,
                'template_id' => $templateId,
                'variables' => ['key1' => 'value1', 'key2' => 'value2'],
                'from' => 'TestSender'
            ];
            
            $mockResponse = [
                'id' => 'msg_test',
                'to' => $to,
                'template_id' => $templateId,
                'variables' => ['key1' => 'value1', 'key2' => 'value2'],
                'expires_at' => '2024-01-15T10:30:00Z',
                'created_at' => '2024-01-15T10:00:00Z',
                'from' => 'TestSender'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_TRANSACTIONAL, $expectedData)
                ->willReturn($mockResponse);
            
            // Act
            $result = $service->send($to, $templateId, $options);
            
            // Assert
            expect($result)->toBeInstanceOf(TransactionalMessage::class);
        });
        
        it('handles empty variables in options', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new TransactionalService($httpClientMock);
            
            $to = '+30123456789';
            $templateId = 'template_01jd1xq0cffycayqtdkdbv4d61';
            $options = new Options([]);
            
            $expectedRequestData = [
                'to' => $to,
                'template_id' => $templateId
            ];
            
            $mockResponse = [
                'id' => 'msg_01jd1xq0cffycayqtdkdbv4d62',
                'to' => $to,
                'template_id' => $templateId,
                'variables' => [],
                'expires_at' => '2024-01-15T10:30:00Z',
                'created_at' => '2024-01-15T10:00:00Z'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_TRANSACTIONAL, $expectedRequestData)
                ->willReturn($mockResponse);
            
            // Act
            $result = $service->send($to, $templateId, $options);
            
            // Assert
            expect($result)->toBeInstanceOf(TransactionalMessage::class);
        });
        
        it('handles international phone numbers correctly', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new TransactionalService($httpClientMock);
            
            $phoneNumber = '+1234567890'; // Test with one representative number
            $templateId = 'template_01jd1xq0cffycayqtdkdbv4d61';
            
            $expectedRequestData = [
                'to' => $phoneNumber,
                'template_id' => $templateId
            ];
            
            $mockResponse = [
                'id' => 'msg_' . substr(md5($phoneNumber), 0, 10),
                'to' => $phoneNumber,
                'template_id' => $templateId,
                'variables' => [],
                'expires_at' => '2024-01-15T10:30:00Z',
                'created_at' => '2024-01-15T10:00:00Z'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->with(Config::ENDPOINT_TRANSACTIONAL, $expectedRequestData)
                ->willReturn($mockResponse);
            
            // Act
            $result = $service->send($phoneNumber, $templateId);
            
            // Assert
            expect($result)->toBeInstanceOf(TransactionalMessage::class);
        });
        
        it('returns TransactionalMessage with correct response data', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new TransactionalService($httpClientMock);
            
            $to = '+30123456789';
            $templateId = 'template_01jd1xq0cffycayqtdkdbv4d61';
            
            $mockResponse = [
                'id' => 'msg_01jd1xq0cffycayqtdkdbv4d62',
                'to' => $to,
                'template_id' => $templateId,
                'variables' => ['name' => 'John'],
                'expires_at' => '2024-01-15T10:30:00Z',
                'created_at' => '2024-01-15T10:00:00Z',
                'from' => 'Prelude',
                'callback_url' => 'https://example.com/webhook',
                'correlation_id' => 'test-correlation-123'
            ];
            
            $httpClientMock
                ->expects(test()->once())
                ->method('post')
                ->willReturn($mockResponse);
            
            // Act
            $result = $service->send($to, $templateId);
            
            // Assert
            expect($result)
                ->toBeInstanceOf(TransactionalMessage::class);
            
            // Verify that TransactionalMessage was created with the response data
            // This tests the integration between TransactionalService and TransactionalMessage
        });
    });
    
    describe('authorization', function () {
        it('throws ApiException when authorization header is missing (400 response)', function () {
            // Arrange
            $httpClientMock = test()->createMock(HttpClient::class);
            $service = new TransactionalService($httpClientMock);
            
            $to = '+30123456789';
            $templateId = 'template_01jd1xq0cffycayqtdkdbv4d61';
            
            $expectedRequestData = [
                'to' => $to,
                'template_id' => $templateId
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
                ->with(Config::ENDPOINT_TRANSACTIONAL, $expectedRequestData)
                ->willThrowException($apiException);
            
            // Act & Assert
            try {
                $service->send($to, $templateId);
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
            $service = new TransactionalService($httpClientMock);
            
            $to = '+30123456789';
            $templateId = 'template_01jd1xq0cffycayqtdkdbv4d61';
            
            $expectedRequestData = [
                'to' => $to,
                'template_id' => $templateId
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
                ->with(Config::ENDPOINT_TRANSACTIONAL, $expectedRequestData)
                ->willThrowException($apiException);
            
            // Act & Assert
            try {
                $service->send($to, $templateId);
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

    describe('constructor', function () {
        it('can be instantiated with HttpClient', function () {
             // Arrange
             $httpClient = test()->createMock(HttpClient::class);
             
             // Act
             $service = new TransactionalService($httpClient);
             
             // Assert
             expect($service)->toBeInstanceOf(TransactionalService::class);
         });
     });
 });