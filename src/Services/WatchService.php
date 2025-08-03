<?php

namespace PreludeSo\SDK\Services;

use PreludeSo\SDK\Config\Config;
use PreludeSo\SDK\Http\HttpClient;

use PreludeSo\SDK\Models\DispatchResponse;
use PreludeSo\SDK\Models\PredictResponse;
use PreludeSo\SDK\ValueObjects\Shared\Metadata;
use PreludeSo\SDK\ValueObjects\Shared\Signals;
use PreludeSo\SDK\ValueObjects\Shared\Target;
use PreludeSo\SDK\ValueObjects\Watch\Event;
use PreludeSo\SDK\ValueObjects\Watch\Feedback;

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

    /**
     * Send feedbacks about verifications
     *
     * @param Feedback[] $feedbacks Array of feedback objects
     * @return array
     */
    public function sendFeedback(array $feedbacks): array
    {
        $requestData = [
            'feedbacks' => array_map(fn(Feedback $feedback) => $feedback->toArray(), $feedbacks)
        ];

        return $this->_httpClient->post(Config::ENDPOINT_WATCH_FEEDBACK, $requestData);
    }

    /**
     * Dispatch events to the Watch API
     *
     * @param Event[] $events Array of event objects
     * @return DispatchResponse
     */
    public function dispatchEvents(array $events): DispatchResponse
    {
        $requestData = [
            'events' => array_map(fn(Event $event) => $event->toArray(), $events)
        ];

        $response = $this->_httpClient->post(Config::ENDPOINT_WATCH_EVENT, $requestData);

        return DispatchResponse::fromArray($response);
    }
}