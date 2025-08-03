<?php

namespace PreludeSo\SDK\Enums;

/**
 * Phone line types for different categories of phone numbers
 */
enum LineType: string
{
    case CALLING_CARDS = 'calling_cards';
    case FIXED_LINE = 'fixed_line';
    case ISP = 'isp';
    case LOCAL_RATE = 'local_rate';
    case MOBILE = 'mobile';
    case OTHER = 'other';
    case PAGER = 'pager';
    case PAYPHONE = 'payphone';
    case PREMIUM_RATE = 'premium_rate';
    case SATELLITE = 'satellite';
    case SERVICE = 'service';
    case SHARED_COST = 'shared_cost';
    case SHORT_CODES_COMMERCIAL = 'short_codes_commercial';
    case TOLL_FREE = 'toll_free';
    case UNIVERSAL_ACCESS = 'universal_access';
    case UNKNOWN = 'unknown';
    case VPN = 'vpn';
    case VOICE_MAIL = 'voice_mail';
    case VOIP = 'voip';
}