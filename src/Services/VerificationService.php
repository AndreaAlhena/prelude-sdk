<?php

namespace PreludeSo\SDK\Services;

use PreludeSo\SDK\Config\Config;
use PreludeSo\SDK\Exceptions\PreludeException;
use PreludeSo\SDK\Http\HttpClient;
use PreludeSo\SDK\Models\Verification;
use PreludeSo\SDK\Models\VerificationResult;
use PreludeSo\SDK\ValueObjects\Shared\Metadata;
use PreludeSo\SDK\ValueObjects\Shared\Signals;
use PreludeSo\SDK\ValueObjects\Shared\Target;
use PreludeSo\SDK\ValueObjects\Verify\Options;

/**
 * Verification Service for OTP verification
 */
final class VerificationService
{
    /**
     * Create a new Verification service instance
     * 
     * @param HttpClient $httpClient
     */
    public function __construct(private HttpClient $_httpClient)
    {
        //
    }

    /**
     * Create a verification for a phone number or email address
     * 
     * @param Target $target The phone number (E.164 format) or email address
     * @param Signals|null $signals Device and user signals for fraud detection
     * @param Options|null $options Additional options (template_id, variables)
     * @param Metadata|null $metadata Additional metadata for the verification
     * @param string $dispatchId Optional dispatch identifier
     * @return Verification
     * @throws PreludeException
     */
    public function create(Target $target, ?Signals $signals = null, ?Options $options = null, ?Metadata $metadata = null, string $dispatchId = ''): Verification
    {
        $data = ['target' => $target->toArray()];

        if ($options) {
            $data['options'] = $options->toArray();
        }

        if ($signals) {
            $data['signals'] = $signals->toArray();
        }

        if ($metadata) {
            $data['metadata'] = $metadata->toArray();
        }

        if ($dispatchId !== '') {
            $data['dispatch_id'] = $dispatchId;
        }

        $response = $this->_httpClient->post(Config::ENDPOINT_VERIFICATION, $data);

        return new Verification($response);
    }

    /**
     * Check/verify an OTP code
     * 
     * @param Target $target The phone number (E.164 format) or email address
     * @param string $code The OTP code to verify
     * @return VerificationResult
     * @throws PreludeException
     */
    public function check(Target $target, string $code): VerificationResult
    {
        $data = [
            'code' => $code,
            'target' => $target->toArray()
        ];

        $response = $this->_httpClient->post(Config::ENDPOINT_VERIFICATION_CHECK, $data);

        return new VerificationResult($response);
    }

    /**
     * Resend an OTP for an existing verification
     * 
     * @param string $verificationId The verification ID
     * @return Verification
     * @throws PreludeException
     */
    public function resendOtp(string $verificationId): Verification
    {
        $response = $this->_httpClient->post(Config::ENDPOINT_VERIFICATION . '/' . $verificationId . '/resend');

        return new Verification($response);
    }
}
