<?php

namespace PreludeSo\SDK\Enums;

enum WebhookEventType: string
{
    case TRANSACTIONAL_MESSAGE_CREATED = 'transactional.message.created';
    case TRANSACTIONAL_MESSAGE_DELIVERED = 'transactional.message.delivered';
    case TRANSACTIONAL_MESSAGE_FAILED = 'transactional.message.failed';
    case TRANSACTIONAL_MESSAGE_PENDING_DELIVERY = 'transactional.message.pending_delivery';

    case VERIFY_ATTEMPT = 'verify.attempt';
    case VERIFY_AUTHENTICATION = 'verify.authentication';
    case VERIFY_DELIVERY_STATUS = 'verify.delivery_status';
}