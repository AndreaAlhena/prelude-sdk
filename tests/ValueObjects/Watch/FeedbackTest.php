<?php

use PreludeSo\SDK\Enums\SignalDevicePlatform;
use PreludeSo\SDK\Enums\TargetType;
use PreludeSo\SDK\ValueObjects\Shared\Metadata;
use PreludeSo\SDK\ValueObjects\Shared\Signals;
use PreludeSo\SDK\ValueObjects\Shared\Target;
use PreludeSo\SDK\ValueObjects\Watch\Feedback;

describe('Watch Feedback', function () {
    it('can be instantiated with only target and type', function () {
        $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
        
        $feedback = new Feedback(
            $target,
            'verification.started'
        );
        
        expect($feedback)->toBeInstanceOf(Feedback::class);
        expect($feedback->getTarget())->toBe($target);
        expect($feedback->getType())->toBe('verification.started');
        expect($feedback->getSignals())->toBeNull();
        expect($feedback->getDispatchId())->toBe('');
        expect($feedback->getMetadata())->toBeNull();
    });
    
    it('can be instantiated with target, type and signals', function () {
        $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
        $signals = new Signals(
            '192.0.2.1',
            '8F0B8FDD-C2CB-4387-B20A-56E9B2E5A0D2',
            SignalDevicePlatform::IOS
        );
        
        $feedback = new Feedback(
            $target,
            'verification.started',
            $signals
        );
        
        expect($feedback)->toBeInstanceOf(Feedback::class);
        expect($feedback->getTarget())->toBe($target);
        expect($feedback->getType())->toBe('verification.started');
        expect($feedback->getSignals())->toBe($signals);
        expect($feedback->getDispatchId())->toBe('');
        expect($feedback->getMetadata())->toBeNull();
    });
    
    it('can be instantiated with all parameters', function () {
        $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
        $signals = new Signals(
            '192.0.2.1',
            '8F0B8FDD-C2CB-4387-B20A-56E9B2E5A0D2',
            SignalDevicePlatform::IOS
        );
        $metadata = new Metadata('test123');
        $dispatchId = '123e4567-e89b-12d3-a456-426614174000';
        
        $feedback = new Feedback(
            $target,
            'verification.completed',
            $signals,
            $dispatchId,
            $metadata
        );
        
        expect($feedback)->toBeInstanceOf(Feedback::class);
        expect($feedback->getTarget())->toBe($target);
        expect($feedback->getType())->toBe('verification.completed');
        expect($feedback->getSignals())->toBe($signals);
        expect($feedback->getDispatchId())->toBe($dispatchId);
        expect($feedback->getMetadata())->toBe($metadata);
    });
    
    it('converts to correct array format with only target and type', function () {
        $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
        
        $feedback = new Feedback(
            $target,
            'verification.started'
        );
        
        $result = $feedback->toArray();
        
        expect($result)->toHaveKey('target');
        expect($result)->toHaveKey('type', 'verification.started');
        expect($result)->not->toHaveKey('signals');
        expect($result)->not->toHaveKey('dispatch_id');
        expect($result)->not->toHaveKey('metadata');
    });
    
    it('converts to correct array format with target, type and signals', function () {
        $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
        $signals = new Signals(
            '192.0.2.1',
            '8F0B8FDD-C2CB-4387-B20A-56E9B2E5A0D2',
            SignalDevicePlatform::IOS
        );
        
        $feedback = new Feedback(
            $target,
            'verification.started',
            $signals
        );
        
        $result = $feedback->toArray();
        
        expect($result)->toHaveKey('target');
        expect($result)->toHaveKey('type', 'verification.started');
        expect($result)->toHaveKey('signals');
        expect($result)->not->toHaveKey('dispatch_id');
        expect($result)->not->toHaveKey('metadata');
    });
    
    it('converts to correct array format with all parameters', function () {
        $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
        $signals = new Signals(
            '192.0.2.1',
            '8F0B8FDD-C2CB-4387-B20A-56E9B2E5A0D2',
            SignalDevicePlatform::IOS
        );
        $metadata = new Metadata('test123');
        $dispatchId = '123e4567-e89b-12d3-a456-426614174000';
        
        $feedback = new Feedback(
            $target,
            'verification.completed',
            $signals,
            $dispatchId,
            $metadata
        );
        
        $result = $feedback->toArray();
        
        expect($result)->toHaveKey('target');
        expect($result)->toHaveKey('type', 'verification.completed');
        expect($result)->toHaveKey('signals');
        expect($result)->toHaveKey('dispatch_id', $dispatchId);
        expect($result)->toHaveKey('metadata');
    });
    
    it('excludes empty dispatch_id from array output', function () {
        $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
        $signals = new Signals(
            '192.0.2.1',
            '8F0B8FDD-C2CB-4387-B20A-56E9B2E5A0D2',
            SignalDevicePlatform::IOS
        );
        
        $feedback = new Feedback(
            $target,
            'verification.started',
            $signals,
            ''
        );
        
        $result = $feedback->toArray();
        
        expect($result)->not->toHaveKey('dispatch_id');
    });
    
    it('excludes empty metadata from array output', function () {
        $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
        $signals = new Signals(
            '192.0.2.1',
            '8F0B8FDD-C2CB-4387-B20A-56E9B2E5A0D2',
            SignalDevicePlatform::IOS
        );
        $emptyMetadata = new Metadata(null);
        
        $feedback = new Feedback(
            $target,
            'verification.started',
            $signals,
            '',
            $emptyMetadata
        );
        
        $result = $feedback->toArray();
        
        expect($result)->not->toHaveKey('metadata');
    });
});