<?php

namespace Prelude\SDK\Enums;

enum VerificationStatus: string
{
    case BLOCKED = 'blocked';
    case RETRY = 'retry';
    case SUCCESS = 'success';
}