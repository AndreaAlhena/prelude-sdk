<?php

use Prelude\SDK\ValueObjects\Verify\Metadata;

describe('Metadata', function () {
    it('can be instantiated with correlation ID', function () {
        $metadata = new Metadata(
            correlationId: 'test-correlation-123'
        );

        expect($metadata)->toBeInstanceOf(Metadata::class);
    });

    it('can be instantiated with no parameters', function () {
        $metadata = new Metadata();

        expect($metadata)->toBeInstanceOf(Metadata::class);
    });

    it('converts to correct array format with correlation ID', function () {
        $metadata = new Metadata(
            correlationId: 'test-correlation-456'
        );

        $result = $metadata->toArray();

        expect($result)->toBe([
            'correlation_id' => 'test-correlation-456'
        ]);
    });

    it('converts to empty array when no parameters provided', function () {
        $metadata = new Metadata();

        $result = $metadata->toArray();

        expect($result)->toBe([]);
    });

    it('handles special characters in correlation ID', function () {
        $metadata = new Metadata(
            correlationId: 'test-correlation-with-special-chars_123@domain.com'
        );

        $result = $metadata->toArray();

        expect($result)->toBe([
            'correlation_id' => 'test-correlation-with-special-chars_123@domain.com'
        ]);
    });

    it('handles empty string correlation ID', function () {
        $metadata = new Metadata(
            correlationId: ''
        );

        $result = $metadata->toArray();

        expect($result)->toBe([
            'correlation_id' => ''
        ]);
    });

    it('maintains immutability of metadata data', function () {
        $metadata = new Metadata(
            correlationId: 'immutable-test-123'
        );

        $result1 = $metadata->toArray();
        $result2 = $metadata->toArray();

        expect($result1)->toEqual($result2);
        
        // Modify one array to ensure they are independent
        $result1['correlation_id'] = 'modified';
        expect($result2['correlation_id'])->toBe('immutable-test-123');
    });
});