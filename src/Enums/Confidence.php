<?php

namespace PreludeSo\SDK\Enums;

/**
 * Confidence levels for dispatch events
 */
enum Confidence: string
{
    case HIGH = 'high';
    case LOW = 'low';
    case MAXIMUM = 'maximum';
    case MINIMUM = 'minimum';
    case NEUTRAL = 'neutral';
}