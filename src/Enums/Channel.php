<?php

namespace Prelude\SDK\Enums;

/**
 * Channel enum for verification channels
 */
enum Channel: string
{
    case RCS = 'rcs';
    case SMS = 'sms';
    case TELEGRAM = 'telegram';
    case VIBER = 'viber';
    case VOICE = 'voice';
    case WHATSAPP = 'whatsapp';
    case ZALO = 'zalo';
}