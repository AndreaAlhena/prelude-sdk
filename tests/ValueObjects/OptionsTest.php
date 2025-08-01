<?php

use Prelude\SDK\ValueObjects\Options;

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
});