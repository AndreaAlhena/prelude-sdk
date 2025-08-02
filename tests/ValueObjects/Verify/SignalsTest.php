<?php

use Prelude\SDK\ValueObjects\Verify\Signals;
use Prelude\SDK\Enums\SignalDevicePlatform;

describe('Signals', function () {
    it('can be instantiated with all parameters', function () {
        $signals = new Signals(
            _ip: '192.0.2.1',
            _deviceId: '8F0B8FDD-C2CB-4387-B20A-56E9B2E5A0D2',
            _devicePlatform: SignalDevicePlatform::IOS,
            _deviceModel: 'iPhone17,2',
            _osVersion: '18.0.1',
            _appVersion: '1.2.34',
            _userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Mobile/15E148 Safari/604.1',
            _isTrustedUser: false
        );

        expect($signals)->toBeInstanceOf(Signals::class);
    });

    it('can be instantiated with no parameters', function () {
        $signals = new Signals();

        expect($signals)->toBeInstanceOf(Signals::class);
    });

    it('converts to correct array format with all parameters', function () {
        $signals = new Signals(
            _ip: '192.0.2.1',
            _deviceId: '8F0B8FDD-C2CB-4387-B20A-56E9B2E5A0D2',
            _devicePlatform: SignalDevicePlatform::IOS,
            _deviceModel: 'iPhone17,2',
            _osVersion: '18.0.1',
            _appVersion: '1.2.34',
            _userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Mobile/15E148 Safari/604.1',
            _isTrustedUser: false
        );

        $result = $signals->toArray();

        expect($result)->toBe([
            'ip' => '192.0.2.1',
            'device_id' => '8F0B8FDD-C2CB-4387-B20A-56E9B2E5A0D2',
            'device_platform' => 'ios',
            'device_model' => 'iPhone17,2',
            'os_version' => '18.0.1',
            'app_version' => '1.2.34',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Mobile/15E148 Safari/604.1',
            'is_trusted_user' => false
        ]);
    });

    it('converts to empty array when no parameters provided', function () {
        $signals = new Signals();

        $result = $signals->toArray();

        expect($result)->toBe([]);
    });

    it('converts to partial array when some parameters provided', function () {
        $signals = new Signals(
            _ip: '10.0.0.1',
            _devicePlatform: SignalDevicePlatform::ANDROID,
            _isTrustedUser: true
        );

        $result = $signals->toArray();

        expect($result)->toBe([
            'ip' => '10.0.0.1',
            'device_platform' => 'android',
            'is_trusted_user' => true
        ]);
    });

    it('handles different device platforms correctly', function () {
        $testCases = [
            ['platform' => SignalDevicePlatform::ANDROID, 'expected' => 'android'],
            ['platform' => SignalDevicePlatform::IOS, 'expected' => 'ios'],
            ['platform' => SignalDevicePlatform::IPADOS, 'expected' => 'ipados'],
            ['platform' => SignalDevicePlatform::TVOS, 'expected' => 'tvos'],
            ['platform' => SignalDevicePlatform::WEB, 'expected' => 'web']
        ];

        foreach ($testCases as $testCase) {
            $signals = new Signals(_devicePlatform: $testCase['platform']);
            $result = $signals->toArray();

            expect($result)->toBe(['device_platform' => $testCase['expected']]);
        }
    });

    it('handles boolean values correctly', function () {
        $trustedSignals = new Signals(_isTrustedUser: true);
        $untrustedSignals = new Signals(_isTrustedUser: false);

        expect($trustedSignals->toArray())->toBe(['is_trusted_user' => true]);
        expect($untrustedSignals->toArray())->toBe(['is_trusted_user' => false]);
    });

    it('handles special characters in string values', function () {
        $signals = new Signals(
            _deviceModel: 'iPhone 15 Pro Max (256GB)',
            _userAgent: 'Mozilla/5.0 (compatible; "Special" & <Test>)'
        );

        $result = $signals->toArray();

        expect($result)->toBe([
            'device_model' => 'iPhone 15 Pro Max (256GB)',
            'user_agent' => 'Mozilla/5.0 (compatible; "Special" & <Test>)'
        ]);
    });
});