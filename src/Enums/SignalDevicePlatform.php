<?php

namespace PreludeSo\SDK\Enums;

enum SignalDevicePlatform: string
{
    case ANDROID = 'android';
    case IPADOS = 'ipados';
    case IOS = 'ios';
    case TVOS = 'tvos';
    case WEB = 'web';
}
