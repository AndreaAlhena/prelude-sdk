<?php

use PreludeSo\SDK\ValueObjects\Verify\Options;
use PreludeSo\SDK\ValueObjects\Verify\AppRealm;
use PreludeSo\SDK\Enums\OptionsMethod;
use PreludeSo\SDK\Enums\OptionsLocale;
use PreludeSo\SDK\Enums\AppRealmPlatform;
use PreludeSo\SDK\Enums\PreferredChannel;

describe('Options', function () {
    it('can be instantiated with template ID only', function () {
        $options = new Options(
            _templateId: 'template-123'
        );

        expect($options)->toBeInstanceOf(Options::class);
    });

    it('can be instantiated with template ID and variables', function () {
        $options = new Options(
            _templateId: 'template-456',
            _variables: ['name' => 'John', 'code' => '123456']
        );

        expect($options)->toBeInstanceOf(Options::class);
    });

    it('converts to correct array format with template ID only', function () {
        $options = new Options(
            _templateId: 'template-789'
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-789',
            'variables' => []
        ]);
    });

    it('converts to correct array format with template ID and variables', function () {
        $options = new Options(
            _templateId: 'welcome-template',
            _variables: [
                'user_name' => 'Alice',
                'verification_code' => '987654',
                'expiry_minutes' => 10
            ]
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'welcome-template',
            'variables' => [
                'user_name' => 'Alice',
                'verification_code' => '987654',
                'expiry_minutes' => 10
            ]
        ]);
    });

    it('handles empty variables array correctly', function () {
        $options = new Options(
            _templateId: 'empty-vars-template',
            _variables: []
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'empty-vars-template',
            'variables' => []
        ]);
    });

    it('handles special characters in template ID', function () {
        $options = new Options(
            _templateId: 'template-with-special_chars@123'
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-with-special_chars@123',
            'variables' => []
        ]);
    });

    it('handles special characters in variables', function () {
        $options = new Options(
            _templateId: 'special-template',
            _variables: [
                'message' => 'Hello "World" & <Test>',
                'email' => 'user@example.com',
                'phone' => '+1-234-567-8900'
            ]
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'special-template',
            'variables' => [
                'message' => 'Hello "World" & <Test>',
                'email' => 'user@example.com',
                'phone' => '+1-234-567-8900'
            ]
        ]);
    });

    it('handles nested arrays in variables', function () {
        $options = new Options(
            _templateId: 'nested-template',
            _variables: [
                'user' => [
                    'name' => 'Bob',
                    'preferences' => [
                        'language' => 'en',
                        'timezone' => 'UTC'
                    ]
                ],
                'metadata' => [
                    'source' => 'web',
                    'campaign_id' => 'summer2024'
                ]
            ]
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'nested-template',
            'variables' => [
                'user' => [
                    'name' => 'Bob',
                    'preferences' => [
                        'language' => 'en',
                        'timezone' => 'UTC'
                    ]
                ],
                'metadata' => [
                    'source' => 'web',
                    'campaign_id' => 'summer2024'
                ]
            ]
        ]);
    });

    it('handles different variable data types', function () {
        $options = new Options(
            _templateId: 'mixed-types-template',
            _variables: [
                'string_var' => 'text',
                'int_var' => 42,
                'float_var' => 3.14,
                'bool_var' => true,
                'null_var' => null,
                'array_var' => [1, 2, 3]
            ]
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'mixed-types-template',
            'variables' => [
                'string_var' => 'text',
                'int_var' => 42,
                'float_var' => 3.14,
                'bool_var' => true,
                'null_var' => null,
                'array_var' => [1, 2, 3]
            ]
        ]);
    });

    it('maintains immutability of options data', function () {
        $options = new Options(
            _templateId: 'immutable-test',
            _variables: ['key' => 'original_value']
        );

        $result1 = $options->toArray();
        $result2 = $options->toArray();

        expect($result1)->toEqual($result2);
        
        // Modify one array to ensure they are independent
        $result1['template_id'] = 'modified';
        $result1['variables']['key'] = 'modified_value';
        
        expect($result2['template_id'])->toBe('immutable-test');
        expect($result2['variables']['key'])->toBe('original_value');
    });

    it('can be instantiated with method property', function () {
        $options = new Options(
            _templateId: 'template-123',
            _variables: [],
            _method: OptionsMethod::VOICE
        );

        expect($options)->toBeInstanceOf(Options::class);
    });

    it('can be instantiated with locale property', function () {
        $options = new Options(
            _templateId: 'template-123',
            _variables: [],
            _locale: OptionsLocale::EN_US
        );

        expect($options)->toBeInstanceOf(Options::class);
    });

    it('can be instantiated with both method and locale properties', function () {
        $options = new Options(
            _templateId: 'template-123',
            _variables: ['name' => 'John'],
            _method: OptionsMethod::AUTO,
            _locale: OptionsLocale::FR_FR
        );

        expect($options)->toBeInstanceOf(Options::class);
    });

    it('converts to correct array format with method property', function () {
        $options = new Options(
            _templateId: 'template-voice',
            _variables: ['code' => '123456'],
            _method: OptionsMethod::VOICE
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-voice',
            'variables' => ['code' => '123456'],
            'method' => 'voice'
        ]);
    });

    it('converts to correct array format with locale property', function () {
        $options = new Options(
            _templateId: 'template-locale',
            _variables: ['user' => 'Alice'],
            _locale: OptionsLocale::ES_ES
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-locale',
            'variables' => ['user' => 'Alice'],
            'locale' => 'es-ES'
        ]);
    });

    it('converts to correct array format with both method and locale properties', function () {
        $options = new Options(
            _templateId: 'template-full',
            _variables: ['name' => 'Bob', 'code' => '789012'],
            _method: OptionsMethod::AUTO,
            _locale: OptionsLocale::DE_DE
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-full',
            'variables' => ['name' => 'Bob', 'code' => '789012'],
            'method' => 'auto',
            'locale' => 'de-DE'
        ]);
    });

    it('excludes null method and locale from array output', function () {
        $options = new Options(
            _templateId: 'template-nulls',
            _variables: ['test' => 'value']
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-nulls',
            'variables' => ['test' => 'value']
        ]);
        expect($result)->not->toHaveKey('method');
        expect($result)->not->toHaveKey('locale');
    });

    it('handles different method enum values correctly', function () {
        $autoOptions = new Options(
            _templateId: 'template-auto',
            _method: OptionsMethod::AUTO
        );

        $voiceOptions = new Options(
            _templateId: 'template-voice',
            _method: OptionsMethod::VOICE
        );

        expect($autoOptions->toArray()['method'])->toBe('auto');
        expect($voiceOptions->toArray()['method'])->toBe('voice');
    });

    it('handles different locale enum values correctly', function () {
        $usOptions = new Options(
            _templateId: 'template-us',
            _locale: OptionsLocale::EN_US
        );

        $frOptions = new Options(
            _templateId: 'template-fr',
            _locale: OptionsLocale::FR_CA
        );

        expect($usOptions->toArray()['locale'])->toBe('en-US');
        expect($frOptions->toArray()['locale'])->toBe('fr-CA');
    });

    it('can be instantiated with appRealm property', function () {
        $appRealm = new AppRealm(
            _platform: AppRealmPlatform::ANDROID,
            _value: 'com.example.app'
        );

        $options = new Options(
            _templateId: 'template-123',
            _appRealm: $appRealm
        );

        expect($options)->toBeInstanceOf(Options::class);
    });

    it('converts to correct array format with appRealm property', function () {
        $appRealm = new AppRealm(
            _platform: AppRealmPlatform::ANDROID,
            _value: 'com.example.android'
        );

        $options = new Options(
            _templateId: 'template-456',
            _appRealm: $appRealm
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-456',
            'variables' => [],
            'app_realm' => [
                'platform' => 'android',
                'value' => 'com.example.android'
            ]
        ]);
    });

    it('can be instantiated with all properties including appRealm', function () {
        $appRealm = new AppRealm(
            _platform: AppRealmPlatform::ANDROID,
            _value: 'com.example.fullapp'
        );

        $options = new Options(
            _templateId: 'template-789',
            _variables: ['key' => 'value'],
            _method: OptionsMethod::AUTO,
            _locale: OptionsLocale::EN_US,
            _appRealm: $appRealm
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-789',
            'variables' => ['key' => 'value'],
            'method' => 'auto',
            'locale' => 'en-US',
            'app_realm' => [
                'platform' => 'android',
                'value' => 'com.example.fullapp'
            ]
        ]);
    });

    it('excludes null appRealm from array output', function () {
        $options = new Options(
            _templateId: 'template-null',
            _appRealm: null
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-null',
            'variables' => []
        ]);
        expect($result)->not->toHaveKey('app_realm');
    });

    it('handles appRealm platform values correctly', function () {
        $appRealm1 = new AppRealm(
            _platform: AppRealmPlatform::ANDROID,
            _value: 'com.first.app'
        );

        $appRealm2 = new AppRealm(
            _platform: AppRealmPlatform::ANDROID,
            _value: 'com.second.app'
        );

        $options1 = new Options(
            _templateId: 'template-first',
            _appRealm: $appRealm1
        );

        $options2 = new Options(
            _templateId: 'template-second',
            _appRealm: $appRealm2
        );

        expect($options1->toArray()['app_realm']['platform'])->toBe('android');
        expect($options1->toArray()['app_realm']['value'])->toBe('com.first.app');
        expect($options2->toArray()['app_realm']['platform'])->toBe('android');
        expect($options2->toArray()['app_realm']['value'])->toBe('com.second.app');
    });

    it('can be instantiated with codeSize', function () {
        $options = new Options(
            _templateId: 'template-123',
            _codeSize: 6
        );

        expect($options)->toBeInstanceOf(Options::class);
    });

    it('converts to correct array format with codeSize', function () {
        $options = new Options(
            _templateId: 'template-456',
            _variables: ['name' => 'John'],
            _codeSize: 8
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-456',
            'variables' => ['name' => 'John'],
            'code_size' => 8
        ]);
    });

    it('excludes codeSize from array when zero', function () {
        $options = new Options(
            _templateId: 'template-789',
            _codeSize: 0
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-789',
            'variables' => []
        ]);
        expect($result)->not->toHaveKey('code_size');
    });

    it('handles codeSize with other properties', function () {
        $appRealm = new AppRealm(
            _platform: AppRealmPlatform::ANDROID,
            _value: 'com.example.app'
        );

        $options = new Options(
            _templateId: 'template-complex',
            _variables: ['user' => 'Alice'],
            _method: OptionsMethod::AUTO,
            _locale: OptionsLocale::EN_US,
            _appRealm: $appRealm,
            _codeSize: 4
        );

        $result = $options->toArray();

        expect($result)->toHaveKey('template_id', 'template-complex');
        expect($result)->toHaveKey('variables', ['user' => 'Alice']);
        expect($result)->toHaveKey('method', 'auto');
        expect($result)->toHaveKey('locale', 'en-US');
        expect($result)->toHaveKey('app_realm');
        expect($result)->toHaveKey('code_size', 4);
    });

    it('can be instantiated with customCode', function () {
        $options = new Options(
            _templateId: 'template-123',
            _customCode: 'CUSTOM123'
        );

        expect($options)->toBeInstanceOf(Options::class);
    });

    it('converts to correct array format with customCode', function () {
        $options = new Options(
            _templateId: 'template-456',
            _variables: ['name' => 'John'],
            _customCode: 'ABC123'
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-456',
            'variables' => ['name' => 'John'],
            'custom_code' => 'ABC123'
        ]);
    });

    it('excludes customCode from array when empty string', function () {
        $options = new Options(
            _templateId: 'template-789',
            _customCode: ''
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-789',
            'variables' => []
        ]);
        expect($result)->not->toHaveKey('custom_code');
    });

    it('handles customCode with other properties', function () {
        $appRealm = new AppRealm(
            _platform: AppRealmPlatform::ANDROID,
            _value: 'com.example.app'
        );

        $options = new Options(
            _templateId: 'template-complex',
            _variables: ['user' => 'Alice'],
            _method: OptionsMethod::AUTO,
            _locale: OptionsLocale::EN_US,
            _appRealm: $appRealm,
            _codeSize: 6,
            _customCode: 'VERIFY789'
        );

        $result = $options->toArray();

        expect($result)->toHaveKey('template_id', 'template-complex');
        expect($result)->toHaveKey('variables', ['user' => 'Alice']);
        expect($result)->toHaveKey('method', 'auto');
        expect($result)->toHaveKey('locale', 'en-US');
        expect($result)->toHaveKey('app_realm');
        expect($result)->toHaveKey('code_size', 6);
        expect($result)->toHaveKey('custom_code', 'VERIFY789');
    });

    it('can be instantiated with callbackUrl', function () {
        $options = new Options(
            _templateId: 'template-123',
            _callbackUrl: 'https://example.com/callback'
        );

        expect($options)->toBeInstanceOf(Options::class);
    });

    it('converts to correct array format with callbackUrl', function () {
        $options = new Options(
            _templateId: 'template-456',
            _variables: ['name' => 'John'],
            _callbackUrl: 'https://api.example.com/webhook'
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-456',
            'variables' => ['name' => 'John'],
            'callback_url' => 'https://api.example.com/webhook'
        ]);
    });

    it('excludes callbackUrl from array when empty string', function () {
        $options = new Options(
            _templateId: 'template-789',
            _callbackUrl: ''
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-789',
            'variables' => []
        ]);
        expect($result)->not->toHaveKey('callback_url');
    });

    it('can be instantiated with preferredChannel', function () {
        $options = new Options(
            _templateId: 'template-123',
            _preferredChannel: PreferredChannel::SMS
        );

        expect($options)->toBeInstanceOf(Options::class);
    });

    it('converts to correct array format with preferredChannel', function () {
        $options = new Options(
            _templateId: 'template-456',
            _variables: ['name' => 'John'],
            _preferredChannel: PreferredChannel::WHATSAPP
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-456',
            'variables' => ['name' => 'John'],
            'preferred_channel' => 'whatsapp'
        ]);
    });

    it('excludes preferredChannel from array when null', function () {
        $options = new Options(
            _templateId: 'template-789',
            _preferredChannel: null
        );

        $result = $options->toArray();

        expect($result)->toBe([
            'template_id' => 'template-789',
            'variables' => []
        ]);
        expect($result)->not->toHaveKey('preferred_channel');
    });

    it('handles all properties together', function () {
        $appRealm = new AppRealm(
            _platform: AppRealmPlatform::ANDROID,
            _value: 'com.example.app'
        );

        $options = new Options(
            _templateId: 'template-full',
            _variables: ['user' => 'Bob'],
            _method: OptionsMethod::VOICE,
            _locale: OptionsLocale::EN_US,
            _appRealm: $appRealm,
            _codeSize: 8,
            _customCode: 'FULL123',
            _callbackUrl: 'https://webhook.example.com',
            _preferredChannel: PreferredChannel::TELEGRAM
        );

        $result = $options->toArray();

        expect($result)->toHaveKey('template_id', 'template-full');
        expect($result)->toHaveKey('variables', ['user' => 'Bob']);
        expect($result)->toHaveKey('method', 'voice');
        expect($result)->toHaveKey('locale', 'en-US');
        expect($result)->toHaveKey('app_realm');
        expect($result)->toHaveKey('code_size', 8);
        expect($result)->toHaveKey('custom_code', 'FULL123');
        expect($result)->toHaveKey('callback_url', 'https://webhook.example.com');
        expect($result)->toHaveKey('preferred_channel', 'telegram');
    });
});