<?php

namespace Prelude\SDK\Services;

use Prelude\SDK\Http\HttpClient;
use Prelude\SDK\Exceptions\PreludeException;
use Prelude\SDK\Models\Verification;
use Prelude\SDK\Models\VerificationResult;
use Prelude\SDK\Config\Config;
use Prelude\SDK\ValueObjects\Verify\Metadata;
use Prelude\SDK\ValueObjects\Verify\Options;
use Prelude\SDK\ValueObjects\Verify\Signals;
use Prelude\SDK\ValueObjects\Verify\Target;

/**
 * Verification Service for OTP verification
 */
class VerificationService
{
    private HttpClient $httpClient;

    /**
     * Create a new Verification service instance
     * 
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
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
    public function create(Target $target, ?Signals $signals, ?Options $options, ?Metadata $metadata = null, string $dispatchId = ''): Verification
    {
        $data = $target->toArray();

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

        $response = $this->httpClient->post(Config::ENDPOINT_VERIFICATION, $data);

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
        $data = array_merge(
            ['code' => $code],
            $target->toArray()
        );

        $response = $this->httpClient->post(Config::ENDPOINT_VERIFICATION_CHECK, $data);

        return new VerificationResult($response);
    }



    /**
     * Get the status of a verification
     * 
     * @param string $verificationId The verification ID
     * @return Verification
     * @throws PreludeException
     */
    public function getVerificationStatus(string $verificationId): Verification
    {
        $response = $this->httpClient->get(Config::ENDPOINT_VERIFICATION . '/' . $verificationId);

        return new Verification($response);
    }

    /**
     * Cancel a pending verification
     * 
     * @param string $verificationId The verification ID to cancel
     * @return bool
     * @throws PreludeException
     */
    public function cancelVerification(string $verificationId): bool
    {
        $response = $this->httpClient->delete(Config::ENDPOINT_VERIFICATION . '/' . $verificationId);

        return $response['success'] ?? false;
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
        $response = $this->httpClient->post(Config::ENDPOINT_VERIFICATION . '/' . $verificationId . '/resend');

        return new Verification($response);
    }
}
