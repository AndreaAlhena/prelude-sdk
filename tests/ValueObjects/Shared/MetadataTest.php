<?php

use Prelude\SDK\ValueObjects\Shared\Metadata;

describe('Shared Metadata', function () {
    it('can be instantiated with no parameters', function () {
        $metadata = new Metadata();

        expect($metadata)->toBeInstanceOf(Metadata::class);
        expect($metadata->getCorrelationId())->toBeNull();
        expect($metadata->toArray())->toBe([]);
    });

    it('can be instantiated with correlation ID', function () {
        $correlationId = 'corr_123456789';
        $metadata = new Metadata(
            _correlationId: $correlationId
        );

        expect($metadata)->toBeInstanceOf(Metadata::class);
        expect($metadata->getCorrelationId())->toBe($correlationId);
    });

    it('converts to correct array format with correlation ID', function () {
        $correlationId = 'user-session-abc123';
        $metadata = new Metadata(
            _correlationId: $correlationId
        );

        $result = $metadata->toArray();

        expect($result)->toBe([
            'correlation_id' => $correlationId
        ]);
    });

    it('excludes null correlation ID from array output', function () {
        $metadata = new Metadata(
            _correlationId: null
        );

        $result = $metadata->toArray();

        expect($result)->toBe([]);
        expect($result)->not->toHaveKey('correlation_id');
    });

    it('handles different correlation ID formats', function () {
        $correlationIds = [
            'simple-id',
            'user_123_session_456',
            'corr-2024-01-15-abc123',
            '550e8400-e29b-41d4-a716-446655440000',
            'tracking.id.with.dots'
        ];

        foreach ($correlationIds as $correlationId) {
            $metadata = new Metadata(
                _correlationId: $correlationId
            );

            expect($metadata->getCorrelationId())->toBe($correlationId);
            expect($metadata->toArray()['correlation_id'])->toBe($correlationId);
        }
    });

    it('handles empty string correlation ID', function () {
        $metadata = new Metadata(
            _correlationId: ''
        );

        expect($metadata->getCorrelationId())->toBe('');
        expect($metadata->toArray())->toBe([
            'correlation_id' => ''
        ]);
    });

    it('maintains immutability of metadata data', function () {
        $correlationId = 'immutable-test-id';
        $metadata = new Metadata(
            _correlationId: $correlationId
        );

        $result1 = $metadata->toArray();
        $result2 = $metadata->toArray();

        expect($result1)->toEqual($result2);
        
        // Modify one array to ensure they are independent
        $result1['correlation_id'] = 'modified';
        
        expect($result2['correlation_id'])->toBe($correlationId);
        expect($metadata->getCorrelationId())->toBe($correlationId);
    });
});