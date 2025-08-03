<?php

use DateTime;
use Prelude\SDK\Models\TransactionalMessage;
use Prelude\SDK\ValueObjects\Transactional\Options;

describe('TransactionalMessage Model', function () {
    it('can be instantiated with basic data', function () {
        $data = [
            'id' => 'msg_123',
            'to' => '+1234567890',
            'template_id' => 'template_456',
            'created_at' => '2023-01-01T00:00:00Z',
            'expires_at' => '2023-01-01T01:00:00Z',
            'from' => 'Prelude',
            'callback_url' => null,
            'correlation_id' => null,
            'variables' => []
        ];

        $message = new TransactionalMessage($data);

        expect($message)->toBeInstanceOf(TransactionalMessage::class);
        expect($message->getId())->toBe('msg_123');
        expect($message->getTo())->toBe('+1234567890');
        expect($message->getTemplateId())->toBe('template_456');
        expect($message->getCreatedAt())->toBeInstanceOf(DateTime::class);
        expect($message->getExpiresAt())->toBeInstanceOf(DateTime::class);
        expect($message->getFrom())->toBe('Prelude');
        expect($message->getCallbackUrl())->toBe('');
        expect($message->getCorrelationId())->toBe('');
        expect($message->getVariables())->toBe([]);
    });

    it('can be instantiated with full data', function () {
        $data = [
            'id' => 'msg_456',
            'to' => '+1234567890',
            'template_id' => 'template_789',
            'created_at' => '2023-01-01T00:00:00Z',
            'expires_at' => '2023-01-01T01:00:00Z',
            'from' => 'Prelude',
            'callback_url' => 'https://example.com/callback',
            'correlation_id' => 'corr_123',
            'variables' => ['name' => 'John', 'code' => '123456']
        ];

        $message = new TransactionalMessage($data);

        expect($message->getId())->toBe('msg_456');
        expect($message->getCallbackUrl())->toBe('https://example.com/callback');
        expect($message->getCorrelationId())->toBe('corr_123');
        expect($message->getVariables())->toBe(['name' => 'John', 'code' => '123456']);
    });

    it('can convert to array', function () {
        $data = [
            'id' => 'msg_789',
            'to' => '+1234567890',
            'template_id' => 'template_abc',
            'created_at' => '2023-01-01T00:00:00Z',
            'expires_at' => '2023-01-01T01:00:00Z',
            'from' => 'Prelude',
            'callback_url' => 'https://example.com/callback',
            'correlation_id' => 'corr_456',
            'variables' => ['user' => 'Jane']
        ];

        $message = new TransactionalMessage($data);
        $array = $message->toArray();

        $expected = [
            'created_at' => '2023-01-01T00:00:00+00:00',
            'expires_at' => '2023-01-01T01:00:00+00:00',
            'id' => 'msg_789',
            'template_id' => 'template_abc',
            'to' => '+1234567890',
            'variables' => ['user' => 'Jane'],
            'from' => 'Prelude',
            'callback_url' => 'https://example.com/callback',
            'correlation_id' => 'corr_456'
        ];

        expect($array)->toBe($expected);
    });
});