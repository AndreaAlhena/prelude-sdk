<?php

namespace Prelude\SDK\Config;

/**
 * Configuration class for Prelude SDK
 */
class Config
{
    public const DEFAULT_BASE_URL = 'https://api.prelude.com';
    public const DEFAULT_TIMEOUT = 30;
    public const DEFAULT_USER_AGENT = 'Prelude-PHP-SDK/1.0.0';
    
    // API endpoints
    public const ENDPOINT_LOOKUP = '/v2/lookup';
    public const ENDPOINT_TRANSACTIONAL = '/v2/transactional';
    public const ENDPOINT_VERIFICATION = '/v2/verification';
    public const ENDPOINT_VERIFICATION_CHECK = '/v2/verification/check';
    public const ENDPOINT_WATCH_EVENT = '/v2/watch/event';
    public const ENDPOINT_WATCH_FEEDBACK = '/v2/watch/feedback';
    public const ENDPOINT_WATCH_PREDICT = '/v2/watch/predict';
    
    // Default verification options
    public const DEFAULT_CODE_LENGTH = 6;
    public const DEFAULT_EXPIRY_MINUTES = 10;
    public const DEFAULT_MAX_ATTEMPTS = 3;
    

    
    /**
     * Get default verification options
     * 
     * @return array
     */
    public static function getDefaultVerificationOptions(): array
    {
        return [
            'code_length' => self::DEFAULT_CODE_LENGTH,
            'expiry_minutes' => self::DEFAULT_EXPIRY_MINUTES,
            'max_attempts' => self::DEFAULT_MAX_ATTEMPTS
        ];
    }
}