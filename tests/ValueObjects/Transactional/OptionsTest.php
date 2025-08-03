<?php

use PreludeSo\SDK\ValueObjects\Transactional\Options;

describe('Options', function () {
    it('can be instantiated with no parameters', function () {
        $options = new Options();

        expect($options)->toBeInstanceOf(Options::class);
        expect($options->getCallbackUrl())->toBe('');
        expect($options->getCorrelationId())->toBe('');
        expect($options->getExpiresAt())->toBe('');
        expect($options->getFrom())->toBe('');
        expect($options->getLocale())->toBe('');
        expect($options->getVariables())->toBe([]);
    });

    it('can be instantiated with all parameters', function () {
        $options = new Options(
            variables: ['name' => 'John', 'code' => '123456'],
            from: 'MyApp',
            locale: 'en-US',
            expiresAt: '2023-12-31T23:59:59Z',
            callbackUrl: 'https://example.com/callback',
            correlationId: 'corr_123'
        );

        expect($options->getCallbackUrl())->toBe('https://example.com/callback');
        expect($options->getCorrelationId())->toBe('corr_123');
        expect($options->getExpiresAt())->toBe('2023-12-31T23:59:59Z');
        expect($options->getFrom())->toBe('MyApp');
        expect($options->getLocale())->toBe('en-US');
        expect($options->getVariables())->toBe(['name' => 'John', 'code' => '123456']);
    });

    it('converts to correct array format with all options', function () {
        $options = new Options(
            variables: ['user' => 'Jane', 'token' => 'abc123'],
            from: 'TestApp',
            locale: 'fr-FR',
            expiresAt: '2023-12-31T23:59:59Z',
            callbackUrl: 'https://example.com/callback',
            correlationId: 'corr_456'
        );

        $expected = [
            'variables' => ['user' => 'Jane', 'token' => 'abc123'],
            'from' => 'TestApp',
            'locale' => 'fr-FR',
            'expires_at' => '2023-12-31T23:59:59Z',
            'callback_url' => 'https://example.com/callback',
            'correlation_id' => 'corr_456'
        ];

        expect($options->toArray())->toBe($expected);
    });

    it('converts to correct array format with partial options', function () {
        $options = new Options(
            variables: ['name' => 'Bob'],
            callbackUrl: 'https://example.com/webhook'
        );

        $expected = [
            'variables' => ['name' => 'Bob'],
            'callback_url' => 'https://example.com/webhook'
        ];

        expect($options->toArray())->toBe($expected);
    });

    it('converts to empty array when no options are set', function () {
        $options = new Options();

        expect($options->toArray())->toBe([]);
    });
});