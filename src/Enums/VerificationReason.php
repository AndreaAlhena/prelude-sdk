<?php

namespace Prelude\SDK\Enums;

enum VerificationReason: string
{
    case EXPIRED_SIGNATURE = 'expired_signature';
    case IN_BLOCK_LIST = 'in_block_list';
    case INVALID_PHONE_LINE = 'invalid_phone_line';
    case INVALID_PHONE_NUMBER = 'invalid_phone_number';
    case INVALID_SIGNATURE = 'invalid_signature';
    case REPEATED_ATTEMPTS = 'repeated_attempts';
    case SUSPICIOUS = 'suspicious';
}