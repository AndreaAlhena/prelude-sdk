<?php

namespace Prelude\SDK\Services;

use Prelude\SDK\Config\Config;
use Prelude\SDK\Http\HttpClient;
use Prelude\SDK\Models\PredictResponse;
use Prelude\SDK\ValueObjects\Shared\Metadata;
use Prelude\SDK\ValueObjects\Shared\Signals;
use Prelude\SDK\ValueObjects\Watch\Target;

class WatchService
{
    public function __construct(
        private HttpClient $_httpClient
    ) {
        //
    }

    /**
     * Predict the outcome of a verification
     *
     * @param Target $target The target to predict for
     * @param Signals $signals Device and user signals
     * @param string|null $dispatchId Optional dispatch ID
     * @param Metadata|null $metadata Optional metadata
     * @return PredictResponse
     */
    public function predictOutcome(
        Target $target,
        Signals $signals,
        ?string $dispatchId = null,
        ?Metadata $metadata = null
    ): PredictResponse {
        $requestData = [
            'target' => $target->toArray(),
            'signals' => $signals->toArray(),
        ];

        if ($dispatchId !== null) {
            $requestData['dispatch_id'] = $dispatchId;
        }

        if ($metadata !== null && !empty($metadata->toArray())) {
            $requestData['metadata'] = $metadata->toArray();
        }

        $response = $this->_httpClient->post(Config::ENDPOINT_WATCH_PREDICT, $requestData);

        return PredictResponse::fromArray($response);
    }
}