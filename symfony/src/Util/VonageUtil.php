<?php

namespace App\Util;

use App\Entity\User;
use Nexmo\Client as NexmoClient;
use Nexmo\Client\Credentials\Basic;
use Nexmo\Verify\Verification;

class VonageUtil
{
    /** @var NexmoClient */
    protected $client;

    public function __construct()
    {
        $this->client = new NexmoClient(
            new Basic(
                $_ENV['VONAGE_API_KEY'],
                $_ENV['VONAGE_API_SECRET']
            )
        );     
    }

    public function sendVerification(User $user)
    {
        // Retrieves the internationalized number using the previous util method created.
        $internationalizedNumber = $this->getInternationalizedNumber($user);

        // If the number is not valid or valid for the country code provided, then return null
        if (!$internationalizedNumber) {
            return null;
        }

        // Initialize the verification process with Vonage
        $verification = new Verification(
            $internationalizedNumber,
            $_ENV['VONAGE_BRAND_NAME'],
            ['workflow_id' => 3]
        );

        return $this->client->verify()->start($verification);
    }

    public function verify(string $requestId, string $verificationCode)
    {
        $verification = new Verification($requestId);

        return $this->client->verify()->check($verification, $verificationCode);
    }

    public function getRequestId(Verification $verification): ?string
    {
        $responseData = $verification->getResponseData();

        if (empty($responseData)) {
            return null;
        }

        return $responseData['request_id'];
    }

    private function getInternationalizedNumber(User $user): ?string
    {
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

        $phoneNumberObject = $phoneNumberUtil->parse(
            $user->getPhoneNumber(),
            $user->getCountryCode()
        );

        if (!$phoneNumberUtil->isValidNumberForRegion(
            $phoneNumberObject,
            $user->getCountryCode())
        ) {
            return null;
        }

        return $phoneNumberUtil->format(
            $phoneNumberObject,
            \libphonenumber\PhoneNumberFormat::INTERNATIONAL
        );
    }
}