<?php

namespace App\Services;

use HTTP_Request2;
use Illuminate\Support\Facades\Log;

class BankApiService
{
    protected $baseUrl;
    protected $username;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = env('BANK_API_BASE_URL');
        $this->username = env('BANK_API_USERNAME');
        $this->apiKey = env('BANK_API_KEY');
    }

    public function inquiryAccount($bankCode, $accountNumber)
    {
        $request = new HTTP_Request2();
        $request->setUrl($this->baseUrl . '/account-inquiry');
        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig([
            'follow_redirects' => true
        ]);

        $request->setHeader([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-oy-username' => $this->username,
            'x-api-key' => $this->apiKey,
        ]);

        $request->setBody(json_encode([
            'bank_code' => $bankCode,
            'account_number' => $accountNumber,
        ]));

        try {
            $response = $request->send();
            if ($response->getStatus() == 200) {
                $responseBody = json_decode($response->getBody(), true);
                if ($responseBody['status']['code'] === '000') {
                    return $responseBody;
                } else {
                    Log::error('Bank API Error', [
                        'error_code' => $responseBody['status']['code'],
                        'error_message' => $responseBody['status']['message']
                    ]);
                    return null;
                }
            } else {
                Log::error('Bank API Non-200 Status', [
                    'status_code' => $response->getStatus(),
                    'response' => $response->getBody()
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Bank API Exception', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
