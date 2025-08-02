<?php

namespace Prelude\SDK\Services;

use Prelude\SDK\Config\Config;
use Prelude\SDK\Exceptions\PreludeException;
use Prelude\SDK\Http\HttpClient;
use Prelude\SDK\Models\LookupResponse;

/**
 * Lookup Service for phone number information
 */
final class LookupService
{
    /**
     * Create a new Lookup service instance
     * 
     * @param HttpClient $httpClient
     */
    public function __construct(private HttpClient $_httpClient)
    {
        //
    }

    /**
     * Lookup information for a phone number
     * 
     * @param string $phoneNumber The phone number in E.164 format
     * @param array $type Optional features (e.g., ['cnam'])
     * @return LookupResponse
     * @throws PreludeException
     */
    public function lookup(string $phoneNumber, array $type = []): LookupResponse
    {
        $queryParams = [];
        if (!empty($type)) {
            $queryParams['type'] = $type;
        }

        $url = Config::ENDPOINT_LOOKUP . '/' . urlencode($phoneNumber);
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        $response = $this->_httpClient->get($url);

        return LookupResponse::fromArray($response);
    }
}