<?php

use PreludeSo\SDK\Models\PredictResponse;

describe('PredictResponse', function () {
    it('can be instantiated with constructor', function () {
        $response = new PredictResponse(
            _id: 'pred_123456789',
            _prediction: 'allow',
            _requestId: 'req_987654321'
        );

        expect($response)->toBeInstanceOf(PredictResponse::class);
        expect($response->getId())->toBe('pred_123456789');
        expect($response->getPrediction())->toBe('allow');
        expect($response->getRequestId())->toBe('req_987654321');
    });

    it('can be created from array', function () {
        $data = [
            'id' => 'pred_abcdef123',
            'prediction' => 'block',
            'request_id' => 'req_fedcba321'
        ];

        $response = PredictResponse::fromArray($data);

        expect($response)->toBeInstanceOf(PredictResponse::class);
        expect($response->getId())->toBe('pred_abcdef123');
        expect($response->getPrediction())->toBe('block');
        expect($response->getRequestId())->toBe('req_fedcba321');
    });

    it('converts to correct array format', function () {
        $response = new PredictResponse(
            _id: 'pred_xyz789',
            _prediction: 'allow',
            _requestId: 'req_abc123'
        );

        $result = $response->toArray();

        expect($result)->toBe([
            'id' => 'pred_xyz789',
            'prediction' => 'allow',
            'request_id' => 'req_abc123'
        ]);
    });

    it('handles different prediction values', function () {
        $predictions = ['allow', 'block', 'review'];

        foreach ($predictions as $prediction) {
            $response = new PredictResponse(
                _id: 'pred_test',
                _prediction: $prediction,
                _requestId: 'req_test'
            );

            expect($response->getPrediction())->toBe($prediction);
            expect($response->toArray()['prediction'])->toBe($prediction);
        }
    });

    it('handles various ID formats', function () {
        $ids = [
            'pred_123456789',
            'prediction-abc-def-123',
            'pred_550e8400-e29b-41d4-a716-446655440000',
            'simple-id'
        ];

        foreach ($ids as $id) {
            $response = new PredictResponse(
                _id: $id,
                _prediction: 'allow',
                _requestId: 'req_test'
            );

            expect($response->getId())->toBe($id);
            expect($response->toArray()['id'])->toBe($id);
        }
    });

    it('handles various request ID formats', function () {
        $requestIds = [
            'req_123456789',
            'request-abc-def-123',
            'req_550e8400-e29b-41d4-a716-446655440000',
            'simple-request-id'
        ];

        foreach ($requestIds as $requestId) {
            $response = new PredictResponse(
                _id: 'pred_test',
                _prediction: 'allow',
                _requestId: $requestId
            );

            expect($response->getRequestId())->toBe($requestId);
            expect($response->toArray()['request_id'])->toBe($requestId);
        }
    });

    it('maintains data integrity through array conversion', function () {
        $originalData = [
            'id' => 'pred_integrity_test',
            'prediction' => 'block',
            'request_id' => 'req_integrity_test'
        ];

        $response = PredictResponse::fromArray($originalData);
        $convertedData = $response->toArray();

        expect($convertedData)->toBe($originalData);
    });

    it('handles round-trip conversion correctly', function () {
        $response1 = new PredictResponse(
            _id: 'pred_roundtrip',
            _prediction: 'review',
            _requestId: 'req_roundtrip'
        );

        $array = $response1->toArray();
        $response2 = PredictResponse::fromArray($array);

        expect($response2->getId())->toBe($response1->getId());
        expect($response2->getPrediction())->toBe($response1->getPrediction());
        expect($response2->getRequestId())->toBe($response1->getRequestId());
    });

    it('maintains immutability of response data', function () {
        $response = new PredictResponse(
            _id: 'pred_immutable',
            _prediction: 'allow',
            _requestId: 'req_immutable'
        );

        $result1 = $response->toArray();
        $result2 = $response->toArray();

        expect($result1)->toEqual($result2);
        
        // Modify one array to ensure they are independent
        $result1['id'] = 'modified';
        $result1['prediction'] = 'modified';
        $result1['request_id'] = 'modified';
        
        expect($result2['id'])->toBe('pred_immutable');
        expect($result2['prediction'])->toBe('allow');
        expect($result2['request_id'])->toBe('req_immutable');
        expect($response->getId())->toBe('pred_immutable');
        expect($response->getPrediction())->toBe('allow');
        expect($response->getRequestId())->toBe('req_immutable');
    });
});