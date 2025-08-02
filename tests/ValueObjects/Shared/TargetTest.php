<?php

use Prelude\SDK\Enums\TargetType;
use Prelude\SDK\ValueObjects\Shared\Target;

describe('Shared Target', function () {
    it('can be instantiated with phone number target', function () {
        $target = new Target(
            _value: '+1234567890',
            _type: TargetType::PHONE_NUMBER
        );

        expect($target)->toBeInstanceOf(Target::class);
        expect($target->getValue())->toBe('+1234567890');
        expect($target->getType())->toBe(TargetType::PHONE_NUMBER);
    });

    it('converts to correct array format', function () {
        $target = new Target(
            _value: '+33123456789',
            _type: TargetType::PHONE_NUMBER
        );

        $result = $target->toArray();

        expect($result)->toBe([
            'value' => '+33123456789',
            'type' => 'phone_number'
        ]);
    });

    it('handles different target types correctly', function () {
        $phoneTarget = new Target(
            _value: '+1234567890',
            _type: TargetType::PHONE_NUMBER
        );

        expect($phoneTarget->toArray()['type'])->toBe('phone_number');
    });

    it('preserves original value format', function () {
        $target = new Target(
            _value: '+44 20 7946 0958',
            _type: TargetType::PHONE_NUMBER
        );

        expect($target->getValue())->toBe('+44 20 7946 0958');
        expect($target->toArray()['value'])->toBe('+44 20 7946 0958');
    });

    it('handles international phone numbers', function () {
        $targets = [
            '+1234567890' => 'US number',
            '+33123456789' => 'French number',
            '+44123456789' => 'UK number',
            '+81123456789' => 'Japanese number'
        ];

        foreach ($targets as $phoneNumber => $description) {
            $target = new Target(
                _value: $phoneNumber,
                _type: TargetType::PHONE_NUMBER
            );

            expect($target->getValue())->toBe($phoneNumber);
            expect($target->getType())->toBe(TargetType::PHONE_NUMBER);
        }
    });
});