<?php

use Prelude\SDK\Http\HttpClient;
use Prelude\SDK\PreludeClient;
use Prelude\SDK\Services\WatchService;

describe('PreludeClient Watch Integration', function () {
    it('can access watch service through client', function () {
        // Arrange
        $client = new PreludeClient('test-api-key');
        
        // Act
        $watchService = $client->watch();
        
        // Assert
        expect($watchService)->toBeInstanceOf(WatchService::class);
    });
    
    it('returns same watch service instance on multiple calls', function () {
        // Arrange
        $client = new PreludeClient('test-api-key');
        
        // Act
        $watchService1 = $client->watch();
        $watchService2 = $client->watch();
        
        // Assert
        expect($watchService1)->toBe($watchService2);
    });
    
    it('creates new watch service instance when HTTP client is changed', function () {
        // Arrange
        $client = new PreludeClient('test-api-key');
        $watchService1 = $client->watch();
        
        $newHttpClient = test()->createMock(HttpClient::class);
        
        // Act
        $client->setHttpClient($newHttpClient);
        $watchService2 = $client->watch();
        
        // Assert
        expect($watchService1)->not->toBe($watchService2);
        expect($watchService2)->toBeInstanceOf(WatchService::class);
    });
});