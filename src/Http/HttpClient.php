<?php

namespace Prelude\SDK\Http;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Prelude\SDK\Exceptions\ApiException;
use Prelude\SDK\Exceptions\PreludeException;
use Psr\Http\Message\ResponseInterface;

/**
 * HTTP Client for Prelude API
 */
class HttpClient
{
    private string $_apiKey;
    private string $_baseUrl;
    private Client $_client;
    
    /**
     * Create a new HTTP client instance
     * 
     * @param string $apiKey
     * @param string $baseUrl
     */
    public function __construct(string $apiKey, string $baseUrl)
    {
        $this->_apiKey = $apiKey;
        $this->_baseUrl = $baseUrl;
        
        $this->_client = new Client([
            'base_uri' => $baseUrl,
            'timeout' => 30,
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'Prelude-PHP-SDK/1.0.0'
            ]
        ]);
    }
    
    /**
     * Make a GET request
     * 
     * @param string $endpoint
     * @param array $query
     * @return array
     * @throws PreludeException
     */
    /**
     * Make a DELETE request
     * 
     * @param string $endpoint
     * @return array
     * @throws PreludeException
     */
    public function delete(string $endpoint): array
    {
        try {
            $response = $this->_client->delete($endpoint);
            
            return $this->_handleResponse($response);
        } catch (GuzzleException $e) {
            throw new ApiException('DELETE request failed: ' . $e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            throw new PreludeException('DELETE request failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
    
    public function get(string $endpoint, array $query = []): array
    {
        try {
            $response = $this->_client->get($endpoint, [
                'query' => $query
            ]);
            
            return $this->_handleResponse($response);
        } catch (GuzzleException $e) {
            throw new ApiException('GET request failed: ' . $e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            throw new PreludeException('GET request failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * Make a POST request
     * 
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws PreludeException
     */
    public function post(string $endpoint, array $data = []): array
    {
        try {
            $response = $this->_client->post($endpoint, [
                'json' => $data
            ]);
            
            return $this->_handleResponse($response);
        } catch (GuzzleException $e) {
            throw new ApiException('POST request failed: ' . $e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            throw new PreludeException('POST request failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * Make a PUT request
     * 
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws PreludeException
     */
    public function put(string $endpoint, array $data = []): array
    {
        try {
            $response = $this->_client->put($endpoint, [
                'json' => $data
            ]);
            
            return $this->_handleResponse($response);
        } catch (GuzzleException $e) {
            throw new ApiException('PUT request failed: ' . $e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            throw new PreludeException('PUT request failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * Handle HTTP response
     * 
     * @param ResponseInterface $response
     * @return array
     * @throws ApiException
     */
    private function _handleResponse(ResponseInterface $response): array
    {
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException('Invalid JSON response: ' . json_last_error_msg(), $statusCode);
        }
        
        if ($statusCode >= 400) {
            $message = $data['message'] ?? $data['error'] ?? 'API request failed';
            throw new ApiException($message, $statusCode);
        }
        
        return $data;
    }
}