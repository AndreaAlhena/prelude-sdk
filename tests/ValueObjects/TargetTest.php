<?php

use Prelude\SDK\ValueObjects\Target;
use Prelude\SDK\Enums\TargetType;

describe('Target', function () {
    it('can be instantiated with phone number', function () {
        $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
        
        expect($target)->toBeInstanceOf(Target::class);
    });
    
    it('can be instantiated with email address', function () {
        $target = new Target('user@example.com', TargetType::EMAIL_ADDRESS);
        
        expect($target)->toBeInstanceOf(Target::class);
    });
    
    it('converts phone number to correct array format', function () {
        $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
        $result = $target->toArray();
        
        expect($result)->toBe([
            'target' => [
                'value' => '+1234567890',
                'type' => 'phone_number'
            ]
        ]);
    });
    
    it('converts email address to correct array format', function () {
        $target = new Target('user@example.com', TargetType::EMAIL_ADDRESS);
        $result = $target->toArray();
        
        expect($result)->toBe([
            'target' => [
                'value' => 'user@example.com',
                'type' => 'email_address'
            ]
        ]);
    });
    
    it('handles special characters in phone number', function () {
        $target = new Target('+44 20 7946 0958', TargetType::PHONE_NUMBER);
        $result = $target->toArray();
        
        expect($result['target']['value'])->toBe('+44 20 7946 0958');
        expect($result['target']['type'])->toBe('phone_number');
    });
    
    it('handles special characters in email address', function () {
        $target = new Target('test+tag@example-domain.co.uk', TargetType::EMAIL_ADDRESS);
        $result = $target->toArray();
        
        expect($result['target']['value'])->toBe('test+tag@example-domain.co.uk');
        expect($result['target']['type'])->toBe('email_address');
    });
    
    it('maintains immutability of target data', function () {
        $target = new Target('+1234567890', TargetType::PHONE_NUMBER);
        $result1 = $target->toArray();
        $result2 = $target->toArray();
        
        // Both calls should return the same data
        expect($result1)->toBe($result2);
        
        // Modifying one result shouldn't affect the other (they are separate arrays)
        $result1['target']['value'] = 'modified';
        expect($result2['target']['value'])->toBe('+1234567890');
    });
    
    it('handles empty string value', function () {
        $target = new Target('', TargetType::PHONE_NUMBER);
        $result = $target->toArray();
        
        expect($result['target']['value'])->toBe('');
        expect($result['target']['type'])->toBe('phone_number');
    });
});