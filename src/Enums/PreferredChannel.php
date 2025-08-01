<?php

namespace Prelude\SDK\Enums;

enum PreferredChannel: string
{
    case RCS = 'rcs';
    case SMS = 'sms';
    case TELEGRAM = 'telegram';
    case VIBER = 'viber';
    case WHATSAPP = 'whatsapp';
    case ZALO = 'zalo';
}