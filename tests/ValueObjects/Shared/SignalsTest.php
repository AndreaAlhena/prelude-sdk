<?php

use PreludeSo\SDK\Enums\SignalDevicePlatform;
use PreludeSo\SDK\ValueObjects\Shared\Signals;

describe('Shared Signals', function () {
    it('can be instantiated with no parameters', function () {
        $signals = new Signals();

        expect($signals)->toBeInstanceOf(Signals::class);
        expect($signals->toArray())->toBe([]);
    });

    it('can be instantiated with all parameters', function () {
        $signals = new Signals(
            _ip: '192.0.2.1',
            _deviceId: '8F0B8FDD-C2CB-4387-B20A-56E9B2E5A0D2',
            _devicePlatform: SignalDevicePlatform::IOS,
            _deviceModel: 'iPhone17,2',
            _osVersion: '18.0.1',
            _appVersion: '1.2.34',
            _userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15',
            _isTrustedUser: false
        );

        expect($signals)->toBeInstanceOf(Signals::class);
        expect($signals->getIp())->toBe('192.0.2.1');
        expect($signals->getDeviceId())->toBe('8F0B8FDD-C2CB-4387-B20A-56E9B2E5A0D2');
        expect($signals->getDevicePlatform())->toBe(SignalDevicePlatform::IOS);
        expect($signals->getDeviceModel())->toBe('iPhone17,2');
        expect($signals->getOsVersion())->toBe('18.0.1');
        expect($signals->getAppVersion())->toBe('1.2.34');
        expect($signals->getUserAgent())->toBe('Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15');
        expect($signals->getIsTrustedUser())->toBe(false);
    });

    it('converts to correct array format with all fields', function () {
        $signals = new Signals(
            _ip: '192.0.2.1',
            _deviceId: '8F0B8FDD-C2CB-4387-B20A-56E9B2E5A0D2',
            _devicePlatform: SignalDevicePlatform::IOS,
            _deviceModel: 'iPhone17,2',
            _osVersion: '18.0.1',
            _appVersion: '1.2.34',
            _userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15',
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
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15',
            'is_trusted_user' => false
        ]);
    });

    it('excludes null values from array output', function () {
        $signals = new Signals(
            _ip: '192.0.2.1',
            _deviceId: null,
            _devicePlatform: SignalDevicePlatform::ANDROID,
            _deviceModel: null,
            _osVersion: '14.0',
            _appVersion: null,
            _userAgent: null,
            _isTrustedUser: true
        );

        $result = $signals->toArray();

        expect($result)->toBe([
            'ip' => '192.0.2.1',
            'device_platform' => 'android',
            'os_version' => '14.0',
            'is_trusted_user' => true
        ]);
        expect($result)->not->toHaveKey('device_id');
        expect($result)->not->toHaveKey('device_model');
        expect($result)->not->toHaveKey('app_version');
        expect($result)->not->toHaveKey('user_agent');
    });

    it('handles different device platforms correctly', function () {
        $iosSignals = new Signals(
            _devicePlatform: SignalDevicePlatform::IOS
        );

        $androidSignals = new Signals(
            _devicePlatform: SignalDevicePlatform::ANDROID
        );

        expect($iosSignals->toArray()['device_platform'])->toBe('ios');
        expect($androidSignals->toArray()['device_platform'])->toBe('android');
    });

    it('handles boolean values correctly', function () {
        $trustedSignals = new Signals(
            _isTrustedUser: true
        );

        $untrustedSignals = new Signals(
            _isTrustedUser: false
        );

        expect($trustedSignals->toArray()['is_trusted_user'])->toBe(true);
        expect($untrustedSignals->toArray()['is_trusted_user'])->toBe(false);
    });

    it('handles partial signal data', function () {
        $signals = new Signals(
            _ip: '10.0.0.1',
            _devicePlatform: SignalDevicePlatform::IOS
        );

        $result = $signals->toArray();

        expect($result)->toBe([
            'ip' => '10.0.0.1',
            'device_platform' => 'ios'
        ]);
        expect(count($result))->toBe(2);
    });

    it('handles complex user agent strings', function () {
        $userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Mobile/15E148 Safari/604.1';
        
        $signals = new Signals(
            _userAgent: $userAgent
        );

        expect($signals->getUserAgent())->toBe($userAgent);
        expect($signals->toArray()['user_agent'])->toBe($userAgent);
    });

    it('handles device model variations', function () {
        $deviceModels = [
            'iPhone17,2',
            'SM-G998B',
            'Pixel 8 Pro',
            'OnePlus 12'
        ];

        foreach ($deviceModels as $model) {
            $signals = new Signals(
                _deviceModel: $model
            );

            expect($signals->getDeviceModel())->toBe($model);
            expect($signals->toArray()['device_model'])->toBe($model);
        }
    });

    it('handles version string formats', function () {
        $versions = [
            'os' => ['18.0.1', '14.4', '13.0', '12.5.7'],
            'app' => ['1.2.34', '2.0.0', '1.0.0-beta.1', '3.14.159']
        ];

        foreach ($versions['os'] as $osVersion) {
            $signals = new Signals(_osVersion: $osVersion);
            expect($signals->getOsVersion())->toBe($osVersion);
        }

        foreach ($versions['app'] as $appVersion) {
            $signals = new Signals(_appVersion: $appVersion);
            expect($signals->getAppVersion())->toBe($appVersion);
        }
    });
});