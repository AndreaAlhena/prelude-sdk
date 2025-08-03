<?php

namespace PreludeSo\SDK\Enums;

/**
 * Verification method enum for API responses
 * Contains only concrete methods that can be returned by the API
 */
enum VerificationMethod: string
{
    case MESSAGE = 'message';
    case SILENT = 'silent';
    case VOICE = 'voice';
}