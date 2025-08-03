<?php

namespace PreludeSo\SDK\Enums;

/**
 * Options method enum for API requests
 * Contains methods that can be specified in verification requests
 */
enum OptionsMethod: string
{
    case AUTO = 'auto';
    case MESSAGE = 'message';
    case VOICE = 'voice';
}
