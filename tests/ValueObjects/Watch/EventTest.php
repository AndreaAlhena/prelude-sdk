<?php

use PreludeSo\SDK\Enums\Confidence;
use PreludeSo\SDK\Enums\TargetType;
use PreludeSo\SDK\ValueObjects\Shared\Target;
use PreludeSo\SDK\ValueObjects\Watch\Event;

it('can be instantiated with all parameters', function () {
    $target = new Target(
        _value: '+1234567890',
        _type: TargetType::PHONE_NUMBER
    );
    
    $event = new Event(
        _target: $target,
        _label: 'fraud_detected',
        _confidence: Confidence::HIGH
    );
    
    expect($event->getTarget())->toBe($target)
        ->and($event->getLabel())->toBe('fraud_detected')
        ->and($event->getConfidence())->toBe(Confidence::HIGH);
});

it('converts to correct array format', function () {
    $target = new Target(
        _value: 'test@example.com',
        _type: TargetType::EMAIL_ADDRESS
    );
    
    $event = new Event(
        _target: $target,
        _label: 'suspicious_activity',
        _confidence: Confidence::NEUTRAL
    );
    
    $array = $event->toArray();
    
    expect($array)->toHaveKey('target')
        ->and($array)->toHaveKey('label')
        ->and($array)->toHaveKey('confidence')
        ->and($array['label'])->toBe('suspicious_activity')
        ->and($array['confidence'])->toBe('neutral')
        ->and($array['target'])->toBeArray();
});

it('works with different confidence levels', function () {
    $target = new Target(
        _value: '+1234567890',
        _type: TargetType::PHONE_NUMBER
    );
    
    $confidenceLevels = [
        Confidence::MAXIMUM,
        Confidence::HIGH,
        Confidence::NEUTRAL,
        Confidence::LOW,
        Confidence::MINIMUM
    ];
    
    foreach ($confidenceLevels as $confidence) {
        $event = new Event(
            _target: $target,
            _label: 'test_event',
            _confidence: $confidence
        );
        
        expect($event->getConfidence())->toBe($confidence)
            ->and($event->toArray()['confidence'])->toBe($confidence->value);
    }
});