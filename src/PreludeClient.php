<?php

namespace Prelude\SDK;

use Prelude\SDK\Exceptions\PreludeException;
use Prelude\SDK\Http\HttpClient;
use Prelude\SDK\Services\LookupService;
use Prelude\SDK\Services\TransactionalService;
use Prelude\SDK\Services\VerificationService;
use Prelude\SDK\Services\WatchService;

/**
 * Main Prelude SDK Client
 * 
 * This is the primary entry point for interacting with Prelude services.
 */
class PreludeClient
{
    private string $_apiKey;
    private string $_baseUrl;
    private HttpClient $_httpClient;
    
    // Service instances
    private ?LookupService $_lookupService = null;
    private ?TransactionalService $_transactionalService = null;
    private ?VerificationService $_verificationService = null;
    private ?WatchService $_watchService = null;
    
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
        
        $this->_apiKey = $apiKey;
        $this->_baseUrl = rtrim($baseUrl, '/');
        $this->_httpClient = new HttpClient($this->_apiKey, $this->_baseUrl);
    }
    
    /**
     * Get the API key
     * 
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->_apiKey;
    }
    
    /**
     * Get the base URL
     * 
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->_baseUrl;
    }

    /**
     * Get Lookup service instance
     * 
     * @return LookupService
     */
    public function lookup(): LookupService
    {
        if ($this->_lookupService === null) {
            $this->_lookupService = new LookupService($this->_httpClient);
        }
        
        return $this->_lookupService;
    }
    
    /**
     * Set a custom HTTP client
     * 
     * @param HttpClient $httpClient
     * @return void
     */
    public function setHttpClient(HttpClient $httpClient): void
    {
        $this->_httpClient = $httpClient;
        
        // Reset service instances to use new HTTP client
        $this->_lookupService = null;
        $this->_transactionalService = null;
        $this->_verificationService = null;
        $this->_watchService = null;
    }

    /**
     * Get Transactional service instance
     * 
     * @return TransactionalService
     */
    public function transactional(): TransactionalService
    {
        if ($this->_transactionalService === null) {
            $this->_transactionalService = new TransactionalService($this->_httpClient);
        }
        
        return $this->_transactionalService;
    }

    /**
     * Get Verification service instance
     * 
     * @return VerificationService
     */
    public function verification(): VerificationService
    {
        if ($this->_verificationService === null) {
            $this->_verificationService = new VerificationService($this->_httpClient);
        }
        
        return $this->_verificationService;
    }

    /**
     * Get Watch service instance
     * 
     * @return WatchService
     */
    public function watch(): WatchService
    {
        if ($this->_watchService === null) {
            $this->_watchService = new WatchService($this->_httpClient);
        }
        
        return $this->_watchService;
    }
}