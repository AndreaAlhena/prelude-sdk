<?php

namespace Prelude\SDK;

use Prelude\SDK\Services\VerificationService;
use Prelude\SDK\Http\HttpClient;
use Prelude\SDK\Exceptions\PreludeException;

/**
 * Main Prelude SDK Client
 * 
 * This is the primary entry point for interacting with Prelude services.
 */
class PreludeClient
{
    private HttpClient $httpClient;
    private string $apiKey;
    private string $baseUrl;
    
    // Service instances
    private ?VerificationService $verificationService = null;
    
    /**
     * Create a new Prelude client instance
     * 
     * @param string $apiKey Your Prelude API key
     * @param string $baseUrl Base URL for Prelude API (optional)
     */
    public function __construct(string $apiKey, string $baseUrl = 'https://api.prelude.com')
    {
        if (empty($apiKey)) {
            throw new PreludeException('API key is required');
        }
        
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->httpClient = new HttpClient($this->apiKey, $this->baseUrl);
    }
    
    /**
     * Get Verification service instance
     * 
     * @return VerificationService
     */
    public function verification(): VerificationService
    {
        if ($this->verificationService === null) {
            $this->verificationService = new VerificationService($this->httpClient);
        }
        
        return $this->verificationService;
    }
    
    /**
     * Get the API key
     * 
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
    
    /**
     * Get the base URL
     * 
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
    
    /**
     * Set a custom HTTP client
     * 
     * @param HttpClient $httpClient
     * @return void
     */
    public function setHttpClient(HttpClient $httpClient): void
    {
        $this->httpClient = $httpClient;
        
        // Reset service instances to use new HTTP client
        $this->verificationService = null;
    }
}