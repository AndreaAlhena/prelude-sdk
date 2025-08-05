<?php

namespace PreludeSo\SDK\Services;

use PreludeSo\SDK\Config\Config;
use PreludeSo\SDK\Enums\LookupType;
use PreludeSo\SDK\Exceptions\PreludeException;
use PreludeSo\SDK\Http\HttpClient;
use PreludeSo\SDK\Models\LookupResponse;

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
     * @param LookupType[] $type Optional features (e.g., [LookupType::CNAM])
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